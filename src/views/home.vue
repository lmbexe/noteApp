<script setup>
import { ref, onMounted, computed } from 'vue';
import api from '../services/api'
import router from '@/router';
import Sidebar from '../components/sidebar.vue';
import NoteCard from '../components/noteCard.vue';

const userLogin = ref('');
const sidebarOpen = ref(false);
const notes = ref([])
const search = ref('')
const filter = ref('all')
const tagSearch = ref('');
const sortDirection = ref('rec');
const authError = ref(false)
const user = ref(null)

onMounted(() => {
  userLogin.value = localStorage.getItem('user') || '';
  fetchUser()
  fetchNotes()
});


const logout = async () =>{
    try{
        await api.logout()
        localStorage.removeItem('user');
        router.push('/login')
    } catch{}
}

const fetchUser = async () =>{
  try{
    const res = await api.getMe()
    user.value = res.data
  }catch{
    user.value = null
  }
}

const fetchNotes = async () =>{
  try {
    const res = await api.getNotes();
    notes.value = res.data.notes;
    authError.value = false;
  } catch (e) {
    notes.value = [];
    
    authError.value = !!(e.response && e.response.status === 401);
  }
}

const deleteNotes = async (note) =>{
  try {
    const response = await api.deleteNote(note.id);
    console.log('Réponse suppression:', response && response.data ? response.data : response);
    await fetchNotes();
  } catch (e) {
    console.error('Erreur lors de la suppression : ', e);
  }
}

const editNote = async (id) => {
  router.push(`/note/${id}`);
};

const togglePin = async (note) => {
  try {
    const updatedNote = { ...note, pinned: !note.pinned };
    await api.updateNote(updatedNote);
    await fetchNotes();
  } catch (e) {
    console.error('Erreur lors de l\'épinglage :', e);
  }
};

const toggleFavorite = async (note) => {
  try {
    const updatedNote = { ...note, favorite: !note.favorite };
    await api.updateNote(updatedNote);
    await fetchNotes();
  } catch (e) {
    console.error('Erreur lors de la mise en favori :', e );
  }
};

const onSearch = (query) => {
  search.value = query;
};
const onFilter = (newFilter) => {
  filter.value = newFilter;
};
const onTagSearch = (tag) => {
  tagSearch.value = tag;
};
const onSort = (direction) => {
  sortDirection.value = direction;
};

const onToggleFavorites = (isFav) => {
  filter.value = isFav ? 'favorites' : 'all';
};

const filteredNotes = computed(() => {
  let result = notes.value;

  if (filter.value === 'favorites') result = result.filter(n => n.favorite);
  if (search.value) result = result.filter(n => n.title.toLowerCase().includes(search.value.toLowerCase()));
  if (tagSearch && tagSearch.value) {
    result = result.filter(n =>
      Array.isArray(n.tags) &&
      n.tags.some(tag => tag.toLowerCase().includes(tagSearch.value.toLowerCase()))
    );
  }
  // Tri dynamique selon la direction choisie
  result = [...result].sort((a, b) => {
    if (b.pinned !== a.pinned) return b.pinned - a.pinned;
    if (sortDirection.value === 'anc') {
      return new Date(a.created_at) - new Date(b.created_at);
    } else {
      return new Date(b.created_at) - new Date(a.created_at);
    }
  });
  return result;
});
</script>

<template>
  <div class="home-container">
    <div class="user-info" v-if="user">
        <span class="user-avatar">
          <svg width="38" height="38" viewBox="0 0 38 38" fill="#e8e0fa">
            <circle cx="19" cy="19" r="19" fill="#e8e0fa"/>
            <circle cx="19" cy="15" r="6" fill="none" stroke="#6d5bb5" stroke-width="2"/>
            <path d="M8 31c2-5 8-5 11-5s9 0 11 5" fill="none" stroke="#6d5bb5" stroke-width="2"/>
          </svg>
        </span>
        <span class="user-login-below">{{ user.user.login }}</span>
        <button class="logout-btn" @click="logout">Déconnexion</button>
    </div>
    <div v-if="!sidebarOpen && !authError" class="burger" @click="sidebarOpen = true">&#9776;</div>
    <transition name="fade">
      <div v-if="sidebarOpen" class="sidebar-overlay" @click="sidebarOpen = false"></div>
    </transition>
    <Sidebar v-if="sidebarOpen"
      :isFavorites="filter === 'favorites'"
      :search="search"
      :tagSearch="tagSearch"
      :sortDirection="sortDirection"
      @update:isFavorites="onToggleFavorites"
      @search="onSearch"
      @search-tags="onTagSearch"
      @filter="onFilter"
      @sort="onSort"
      @close="sidebarOpen = false"
    />
  
   
    <main class="notes-main">
      <h2 v-if="!authError">Mes notes</h2>
      <div v-if="authError" class="auth-error">
        <div class="auth-card">
          <div class="auth-icon">🔒</div>
          <div class="auth-msg">Vous devez être connecté pour accéder à vos notes.</div>
          <router-link to="/login" class="auth-btn">Se connecter</router-link>
        </div>
      </div>
      <div v-else class="notes-list">
        <router-link
           to="/note/new"
          class="note-card add-note-card"
          style="text-decoration: none; color: inherit;">
          <div class="add-note-plus">+</div>
          <div class="add-note-text">Nouvelle note</div>
          <span class="date" style="visibility:hidden;position:absolute;left:16px;bottom:10px;">00/00/0000</span>
        </router-link>
        <NoteCard
          v-for="note in filteredNotes"
          :key="note.id"
          :note="note"
          @click="editNote(note.id)" 
          @pin="togglePin(note)"
          @favorite="toggleFavorite(note)"
          @delete="deleteNotes(note)"
        />
      </div>
    </main>
  </div>
</template>

<style scoped>

.home-container {
  display: flex;
  min-height: 100vh;
  position: relative;
}

.user-info {
  position: fixed;
  top: 24px;
  right: 10vw;
  display: flex;
  flex-direction: column;
  align-items: center;
  z-index: 5000;
  background: none;
  padding: 0;
  border-radius: 0;
  box-shadow: none;
}

.user-info *{
  margin-top: 5px;
}

.logout-btn{
  background-color: #e8e0fa;
  font-size: 1em;
  color: #6d5bb5;
  padding: 6px;
  border: none;
  border-radius: 10px;
}

.logout-btn:hover{
  background:  #ebebff; 
}

.burger{
  position: fixed;
  top: 24px;
  left: 10vw;
  font-size: 3em;
}

.notes-main{
  flex: 1;
  padding: 20vh 38px 38px 10vw;
  margin-left: 0;
}

.add-note-card{
  display: flex;
  justify-content: center;
  flex-direction: column;
  align-items: center;
}

.note-card{
  background-color: white;
  width: 275px;
  height: 175px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.07);
  border-radius: 12px;
  margin-right: 25px;
  margin-top: 25px;
  padding: 18px 16px 12px 16px; 
}

/* .note-card *{
  cursor: pointer;
} */

.note-card:hover{
  background: #f5f5fa;
  box-shadow: 0 8px 18px rgba(80,80,130,0.13);
}

.notes-list{
  margin-top: 20px;
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
}

.auth-error {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  height: 100vh;
  width: 100vw;
  position: fixed;
  left: 0;
  top: 0;
  background: #f7f7f7;
  z-index: 3000;
}
.auth-card {
  background: #fff;
  padding: 38px 32px 32px 32px;
  border-radius: 16px;
  box-shadow: 0 4px 32px rgba(80,80,130,0.10);
  display: flex;
  flex-direction: column;
  align-items: center;
  max-width: 350px;
  width: 100%;
}
.auth-icon {
  font-size: 3.7em;
  margin-bottom: 18px;
  color: #6c5ce7;
}
.auth-msg {
  font-size: 1.23em;
  color: #333;
  margin-bottom: 22px;
  text-align: center;
}
.auth-btn {
  background: #6c5ce7;
  color: #fff;
  border: none;
  border-radius: 20px;
  padding: 11px 36px;
  font-size: 1.12em;
  font-weight: 500;
  text-decoration: none;
  box-shadow: 0 2px 8px rgba(120,120,180,0.09);
  transition: background 0.18s;
  outline: none;
  cursor: pointer;
  margin-top: 8px;
  display: inline-block;
  text-align: center;
}
.auth-btn:hover {
  background: #b9a8f9;
  color: #fff;
}
</style>