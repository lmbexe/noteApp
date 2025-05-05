<script setup>

</script>

<template>
  <router-view />
</template>

<style>
body {
  font-family: 'Segoe UI', Arial, sans-serif;
  background: #f7f7f7;
  margin: 0;
  
}

button, .note-card, .burger{
  cursor: pointer;
}

p, div, span:not([contenteditable="true"]) {
    user-select: none;
    cursor: default;
}

input[type="text"], 
textarea {
    caret-color: auto;
}
</style>
