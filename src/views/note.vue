<script setup>
import { ref, watch, onMounted } from 'vue';
import api from '../services/api';
import router from '../router/index';

// Récupère la prop id passée par le router (toujours définie grâce à props: true ou props: { id: "new" })
const props = defineProps({ id: String });

const id = ref(props.id);

// Si la prop id change (navigation entre notes sans recharger), on met à jour id
watch(() => props.id, (newId) => {
  id.value = newId;
});

const successMsg = ref('');
const loading = ref(false);
const noteCopy = ref({ title: '', body: '', tags: [], id: id.value });
const newTag = ref('');

onMounted(() => {
  chargeNote();
});

function removeTag(idx) {
  noteCopy.value.tags.splice(idx, 1);
}

function addTag() {
  if (newTag.value.trim()) {
    noteCopy.value.tags.push(newTag.value.trim());
    newTag.value = '';
  }
}

const goBack = async () => {
  router.back();
}

const chargeNote = async () => {
  if (noteCopy.id !== 'new') {
      const res = await api.getNotes(noteCopy.value.id);
      const note = res.data.notes.find(n => n.id == noteCopy.value.id);
      if (note) {
        noteCopy.value = JSON.parse(JSON.stringify(note));
      }
    }
    loading.value = false;
}



const saveNote = async () => {
  try {
    let response;
    if (id.value === 'new') {
      response = await api.createNote(noteCopy.value);
    } else {
      const noteToSend = { ...noteCopy.value, id: id.value || noteCopy.value.id };
      response = await api.updateNote(noteToSend);
    }
    successMsg.value = 'Note sauvegardée !';
    setTimeout(() => {
      successMsg.value = '';
      router.push({ path: '/'});
    }, 1000);
  } catch (e) {
    alert('Erreur lors de la sauvegarde : ' + (e && e.response && e.response.data && e.response.data.error ? e.response.data.error : e.message));
    console.error(e);
  }
};
</script>

<template>
<div class="note-edit-outer">
    <div class="note-edit">
      <div v-if="successMsg" class="success-msg">{{ successMsg }}</div>
      <div v-if="loading">Chargement…</div>
      <div v-else class="elements">
        <input v-model="noteCopy.title" placeholder="Titre" class="note-title modern-title" />
        <textarea v-model="noteCopy.body" placeholder="Tapez ici..." class="note-body modern-body"></textarea>
        <div class="tags-list">
          <span v-for="(tag, idx) in noteCopy.tags" :key="tag" class="tag">
            #{{ tag }}
            <button @click="removeTag(idx)">x</button>
          </span>
        </div>
        <div class="actions-row">
          <input v-model="newTag" placeholder="Ajouter des tags..." @keyup.enter="addTag" class="add-tag-input" />
          <button class="add-tag-btn" @click="addTag">Ajouter le tag</button>
        </div>
      </div>
    </div>
    <div class="edit-actions-bar">
      <button class="back-btn" @click="goBack">Retour</button>
      <button class="save-btn" @click="saveNote">Sauvegarder</button>
    </div>
  </div>
</template>

<style scoped>
.note-edit-outer{
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}

.note-edit{
    width: 40%;
    height: 35%;
    background: white;
    padding: 70px;
    border-radius: 30px;
    box-shadow: 0 2px 20px rgba(80,80,130,0.11);
}

.note-title, textarea{
  padding : 10px;
  border-radius: 8px;
  border: 1.5px solid #d3d3e7;
  background: #fafbff;
  outline: none;
  font-size: 1em;
}

.elements{
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

textarea{
    height: 60px;
}

.save-btn{
  background: #6d5bb5;
  color: white;
}

.save-btn:hover{
    background:  #ebebff; 
    color: #6d5bb5;
}

.back-btn{
  background-color: #e8e0fa;
  color: #6d5bb5;
}

.save-btn, .back-btn{
    font-size: 1em;
    padding: 15px;
    border: none;
    border-radius: 15px;
}

.back-btn:hover{
    background:  #ebebff; 
}

.edit-actions-bar{
    width: 50%;
    display: flex;
    justify-content: space-around;
    margin-top: 10vh;
}

.add-tag-btn{
    font-size: 1em;
    background: #6d5bb5;
    color: white;
    border-radius: 8px;
    padding: 5px;
    border: none;
}

.add-tag-input{
    font-size: 1em;
    border-radius: 8px;
    padding: 5px;
    border: 1px solid #d3d3e7;
    outline: none;
}

.actions-row  * {
    margin-right: 10px;
}

.success-msg{
  background-color: #00b894;
  color: #fff; 
  padding: 10px 18px;
  border-radius: 8px; 
  margin-bottom: 16px; 
  text-align: center; 
  font-weight: bold;
}
</style>