import axios from "axios";

const API_URL = "http://localhost/noteApp/backend/endpoints/";

export default {
  async login(login, password) {
    return axios.post(
      API_URL + "login.php",
      { login, password },
      { withCredentials: true }
    );
  },
  async register(email, login, password) {
    return axios.post(
      API_URL + "register.php",
      { email, login, password },
      { withCredentials: true }
    );
  },
  async getMe() {
    return axios.get(API_URL + "me.php", { withCredentials: true });
  },
  async logout() {
    return axios.post(API_URL + "logout.php", {}, { withCredentials: true });
  },

  async getNotes() {
    return axios.get(API_URL + "notes.php", { withCredentials: true });
  },
  async getOneNote(id) {
    return axios.get(API_URL + "notes.php", {
      id,
      withCredentials: true,
    });
  },
  async createNote(note) {
    return axios.post(API_URL + "notes.php", note, { withCredentials: true });
  },
  async updateNote(note) {
    return axios({
      method: "put",
      url: API_URL + "notes.php",
      data: note,
      withCredentials: true,
    });
  },
  async deleteNote(id) {
    return axios({
      method: "delete",
      url: API_URL + "notes.php",
      data: { id },
      headers: { "Content-Type": "application/json" },
      withCredentials: true,
    });
  },
};
