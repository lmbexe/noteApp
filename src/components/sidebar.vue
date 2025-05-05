<script setup>
const props = defineProps({
  search: String,
  tagSearch: String,
  dateDirection: String,
  isFavorites: Boolean
});
</script>

<template>
<transition name="slide">
    <aside class="sidebar">
      <div class="sidebar-header">
        <button class="close-btn" @click="$emit('close')">&times;</button>
      </div>
      <div class="search">
  <input type="text" placeholder="Rechercher une note..." :value="search" @input="$emit('search', $event.target.value)" />
</div>
<div class="search tag-search">
  <input type="text" placeholder="Rechercher par tag..." :value="tagSearch" @input="$emit('search-tags', $event.target.value)" />
</div>

      <div class="filters">
        <div class="filter-title">Trier par</div>
        <div class="filter-date-dropdown">
  <label for="date-sort" class="filter-label">Date</label>
  <select id="date-sort" class="date-sort-select" :value="dateDirection" @change="$emit('sort', $event.target.value)">
    <option value="rec">Les plus récentes</option>
    <option value="anc">Les plus anciennes</option>
  </select>
</div>
        <div class="filter-row">
          <span>Favoris</span>
          <label class="switch">
            <input type="checkbox" :checked="isFavorites" @change="$emit('update:isFavorites', $event.target.checked); $emit('filter', $event.target.checked ? 'favorites' : 'all')" />
            <span class="slider"></span>
          </label>
        </div>
      </div>
    </aside>
  </transition>
</template>

<style scoped>
.sidebar{
  background-color: rgba(128, 128, 128, 0.094);
  width: 225px;
}

.sidebar div{
  margin-left: 10px;
}

.sidebar div *{
  margin: 5px;
}

.sidebar-header{
  display: flex;
}
.sidebar-header {
  justify-content: end;
  align-items: end;
}

.search input{
  padding: 8px;
  border-radius: 7px;
  outline: none;
  border: 1.5px solid #d3d3e7;
  background: #fafbff;
}

.search input:focus{
  border-color: #a5a5e7;
  background: #f3f3ff;
}

.filter-title{
  color: #6d5bb5;
}

.close-btn{
  border-radius: 7px;
  border: 1px solid #d3d3e7;
}

.close-btn:hover{
  background: #f3f3ff;
}

#date-sort{
  border: 1px solid #d3d3e7;
  border-radius: 5px;
  padding: 3px;
  outline: none;
}

#date-sort:focus{
  border-color: #a5a5e7;
  background: #f3f3ff;
}


</style>