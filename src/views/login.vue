<script setup>
import { ref } from 'vue';
import api from '../services/api'
import router from '@/router';

const login = ref('');
const password = ref('');
const error = ref('');
const success = ref(false);

const handleLogin = async () => {
  
  try{
    await api.login(login.value, password.value)
    localStorage.setItem('user', login.value);
    router.push('/')
  } catch(e){
    error.value = e.response.data.error;
  }
  
};
</script>

<template>
  <div class="container">
      <div class="form-container">
        <h1>Connexion</h1>
        <form @submit.prevent="handleLogin">
          <input v-model="login" placeholder="Email ou nom d'utilisateur" required/>
          <input v-model="password" type="password" placeholder="Mot de passe" required/>
          <button class="submit-btn" type="submit">Se connecter</button>
          <p class="switch-link">Pas de compte ? <router-link to="/register">Inscription</router-link></p>
          <p v-if="error" class="error">{{ error }}</p>
        </form>
      </div>
  </div>
  
</template>

<style scoped>

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
  align-items: center;
  flex-direction: column;
}

.form-container form{
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction : column;
}

form input {
  padding : 10px;
  border-radius: 8px;
  border: 1.5px solid #d3d3e7;
  background: #fafbff;
  outline: none;
  font-size: 1em;
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
}

form *{
  margin: 10px;
}

.error{
  color: red;
}
</style>
