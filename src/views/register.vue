<script setup>
import { ref } from 'vue';
import api from '../services/api';
import router from '@/router';

const login = ref('');
const email = ref('');
const password = ref('');
const error = ref('');
const success = ref(false);

const handleRegister = async () => {
  try {
    await api.register(email.value, login.value, password.value);
    success.value = true;
    setTimeout(() => {
      router.push('/login');
    }, 2000);
  }
  catch (e) {
    console.error(e, e.response);
    error.value = (e.response && e.response.data && e.response.data.error) 
      ? e.response.data.error 
      : 'Erreur lors de l\'inscription.';
  }
};
</script>

<template>
    <div class="container">
        <div class="form-container">
            <h1>Inscription</h1>
            <form v-if="!success" @submit.prevent="handleRegister">
                <input id="login" v-model="login" placeholder="Nom d'utilisateur">
                <input id="email" v-model="email" placeholder="Adresse mail">
                <input id="password" v-model="password" type="password" placeholder="Mot de passe">
                <button class="submit-btn" type="submit">S'inscrire</button>
                <p class="switch-link">Déjà inscrit ? <router-link to="/login">Connexion</router-link></p>
                <p v-if="error" class="error">{{ error }}</p>
            </form>
            <p v-else class="success">Inscription réussie ! Redirection en cours...</p>
        </div>
    </div>
</template>

<style scoped>
.success {
  color: green;
  font-weight: bold;
  margin-top: 20px;
}

.container{
  width: 100vw;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: #f7f7f7;
}

.form-container{
  width: 400px;
  height: 400px;
  background-color: white;
  border-radius: 30px;
  box-shadow: 0 2px 20px rgba(80,80,130,0.11);
  display: flex;
  justify-content: center;
  flex-direction: column;
  align-items: center;
}


.form-container form{
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

form input {
  padding : 10px;
  border-radius: 8px;
  border: 1.5px solid #d3d3e7;
  background: #fafbff;
  outline: none;
  font-size: 1em;
  margin: 10px;
}

form input:focus{
  border-color: #a5a5e7;
  background: #f3f3ff;
}

.submit-btn{
  width: 55%;
  background: #6d5bb5;
  color: white;
  border: none;
  padding: 10px;
  border-radius: 8px;
  font-size: 1em;
  margin: 10px;
}


.error{
  color: red;
}
</style>