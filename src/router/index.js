import { createRouter, createWebHistory } from "vue-router";
import Login from "../views/login.vue";
import Register from "../views/register.vue";
import Home from "../views/home.vue";
import Note from "../views/note.vue";

const routes = [
  { path: "/", name: "Home", component: Home },
  { path: "/login", name: "Login", component: Login },
  { path: "/register", name: "Register", component: Register },
  {
    path: "/note/new",
    name: "NoteCreate",
    component: Note,
    props: { id: "new" },
  },
  {
    path: "/note/:id",
    name: "NoteEdit",
    component: Note,
    props: true,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

//oblige l'auth sauf sur /login et /register
router.beforeEach((to, from, next) => {
  const isAuthenticated = !!localStorage.getItem("user");
  if (!isAuthenticated && to.path !== "/login" && to.path !== "/register") {
    next("/login");
  } else {
    next();
  }
});

export default router;
