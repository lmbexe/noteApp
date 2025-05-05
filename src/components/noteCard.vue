<script setup>
 import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue';

const props = defineProps({
  note: {
    type: Object,
    default: () => ({ title: '', body: '', pinned: false, favorite: false, created_at: '' }),
    required: true,
  },
});

onMounted(() => {
  computeTagsToShow();
  window.addEventListener('closeAllMenus', closeMenuFromGlobal);
  document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside);
});

const emit = defineEmits(['favorite', 'pin', 'delete']);

const showMenu = ref(false);
const visibleTags = ref([]);
const hiddenCount = ref(0);
const tagsRef = ref(null);

const handleClickOutside = (event) => {
  if (showMenu.value && !event.target.closest('.note-menu') && !event.target.closest('.menu')) {
    showMenu.value = false;
  }
};

const formatDate = (date) => {
  if (!date) return 'Date inconnue';
  const d = new Date(date);
  return d.toLocaleDateString(); // Formate la date sinon erreur
};

const openMenu = async () => {
  window.dispatchEvent(new Event('closeAllMenus'));
  showMenu.value = true;
}

const closeMenuFromGlobal = async () => {
  showMenu.value = false;
}

const deleteNote = async () => {
  showMenu.value = false;
  $emit('delete', note.value);
}

const computeTagsToShow = () => {
  nextTick(() => {
    const tagContainer = tagsRef.value;
    const dateEl = tagContainer?.parentNode?.querySelector('.date');
    if (!tagContainer || !dateEl) return;
    const footerWidth = tagContainer.parentNode.offsetWidth;
    const dateWidth = dateEl.offsetWidth + 12; // 12px de marge
    let total = 0;
    let visible = [];
    let hidden = 0;
    for (let i = 0; i < props.note.tags.length; i++) {
      // Crée un span temporaire pour mesurer la largeur du tag
      const temp = document.createElement('span');
      temp.className = 'tag';
      temp.style.visibility = 'hidden';
      temp.innerText = '#' + props.note.tags[i];
      tagContainer.appendChild(temp);
      const tagWidth = temp.offsetWidth + 4; // 4px de gap
      tagContainer.removeChild(temp);
      if (total + tagWidth + dateWidth < footerWidth - 10) {
        total += tagWidth;
        visible.push(props.note.tags[i]);
      } else {
        hidden = props.note.tags.length - visible.length;
        break;
      }
    }
    visibleTags.value = visible;
    hiddenCount.value = hidden;
  });
};
</script>

<template>
  <div v-if="note" class="note-card" @click="onCardClick">
    <div class="note-header">
      <h3>{{ note.title }}</h3>
      <div class="note-actions">
        <span class="pin" :class="{active: note.pinned}" @click.stop="$emit('pin', note)">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
            <g>
              <ellipse cx="10" cy="5.5" rx="3" ry="3.5" :fill="note.pinned ? '#f1c40f' : '#fff'" stroke="#bfa700" stroke-width="1.2"/>
              <rect x="9.1" y="8" width="1.8" height="7.5" rx="0.9" :fill="note.pinned ? '#bfa700' : '#bbb'"/>
              <ellipse cx="10" cy="16.2" rx="1.1" ry="0.7" :fill="note.pinned ? '#bfa700' : '#bbb'"/>
            </g>
          </svg>
        </span>
        <span class="fav" :class="{active: note.favorite}" @click.stop="$emit('favorite', note)">
          <svg width="18" height="18" fill="none" :stroke="note.favorite ? '#e74c3c' : '#bbb'" stroke-width="2">
            <path d="M9 15l-5.5-5.5a3.5 3.5 0 015-5l.5.5.5-.5a3.5 3.5 0 015 5L9 15z"/>
          </svg>
        </span>
        <span class="menu" @click.stop="openMenu()">
          <svg width="22" height="22" fill="#555" class="vertical-dots">
            <circle cx="11" cy="5.5" r="1.7"/>
            <circle cx="11" cy="11" r="1.7"/>
            <circle cx="11" cy="16.5" r="1.7"/>
          </svg>
          <div v-if="showMenu" class="note-menu">
            <button @click.stop="$emit('delete', note)">Supprimer</button>
          </div>
        </span>
      </div>
    </div>
    <div class="note-body">{{ note.body }}</div>

    <div class="note-footer">
      <span class="date">{{ formatDate(note.created_at) }}</span>
      <div class="tags-bottom" ref="tagsRef">
        <span v-for="(tag, idx) in visibleTags" :key="tag" class="tag">#{{ tag }}</span>
        <span v-if="hiddenCount > 0" class="tag">...</span>
      </div>
    </div>
  </div>
  <div v-else>
    <p>Note non définie</p>
  </div>
</template>

<style scoped>

.note-card {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  transition: box-shadow 0.2s;
  position: relative;
}

.note-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}
.note-actions {
  display: flex;
  flex-direction: row;
  align-items: center;
  margin-top: 2px;
  gap: 6px;
}

.note-actions {
  display: flex;
  flex-direction: row;
  align-items: center;
  margin-top: 2px;
  gap: 6px;
}
.note-actions span {
  display: flex;
  align-items: center;
  margin-left: 0;
  cursor: pointer;
}
.note-actions svg {
  display: block;
  margin-top: 0;
  margin-bottom: 0;
}

.pin.active svg path {
  stroke: #f1c40f;
}
.fav.active svg path {
  fill: #e74c3c;
}

.menu svg {
  vertical-align: middle;
}
.note-menu {
  position: absolute;
  right: 0;
  top: 50px;
  background: white;
  border: 1px solid #eee;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  z-index: 10;
  display: flex;
  flex-direction: column;
}
.note-menu button {
  background: none;
  border: none;
  padding: 8px 16px;
  text-align: left;
  cursor: pointer;
  font-size: 1em;
}
.note-menu button:hover {
  background: #f5f5f5;
}

.note-body {
  margin: 12px 0 8px 0;
  color: #444;
  font-size: 1em;
  min-height: 48px;
}
.note-tags {
  margin-bottom: 6px;
}
.tag {
  background: #e3eaff;
  color: #6c5ce7;
  border-radius: 4px;
  padding: 2px 7px;
  margin-right: 6px;
  font-size: 0.92em;
}
.note-footer {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  position: relative;
}
.tags-bottom {
  position: absolute;
  right: 0;
  bottom: 0;
  display: flex;
  gap: 4px;
}
.tag {
  background: #eee;
  border-radius: 8px;
  padding: 2px 8px;
  margin-right: 4px;
  font-size: 0.85em;
}
</style>