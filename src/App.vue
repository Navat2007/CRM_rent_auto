<script setup>
import {RouterView} from 'vue-router';
import axios from "axios";
import moment from "moment";

import {useAuthStore} from "/src/store/";

const expireDays = 30;
const authStore = useAuthStore();

console.log(authStore.user);

axios.interceptors.response.use((response) => {
  if (response.data !== undefined && response.data !== null && typeof response.data !== 'object') {
    if(response?.data?.includes('Warning')) {
      console.log(response.data);
    }
  }

  const version = parseInt(window.localStorage.getItem('version'));

  fetch(`${import.meta.env.VITE_API_URL}/admin/check_version.php`, {
    method: 'POST',
    body: new FormData()
  })
      .then(function (response) {
        return response.json();
      })
      .then((result) => {
        window.localStorage.setItem('version', result.params);

        if (version && version !== result.params) {
          if ('URL' in window) {
            const url = new URL(window.location.href);
            url.searchParams.set('reloadTime', Date.now().toString());
            window.location.href = url.toString();
          } else {
            window.location.href = window.location.origin
                + window.location.pathname
                + window.location.search
                + (window.location.search ? '&' : '?')
                + 'reloadTime='
                + Date.now().toString()
                + window.location.hash;
          }
        }

        if(window.location.href.includes("?reloadTime"))
        {
          const url = new URL(window.location.href);
          url.searchParams.delete('reloadTime');
          window.location.href = url.toString();
        }
      });

  if (response?.data?.error === 3) {
    authStore.logout();
  }
  return response;
}, (error) => {
  return Promise.reject(error.message);
});

if (authStore.user) {
  let expireDate = moment(authStore.user.token_created_at.date, 'YYYY-MM-DD').add(expireDays, 'days');

  if (expireDate.isAfter(moment())) {
    axios.defaults.headers.post['Authorization'] = `${authStore.user.token}&${authStore.user.id}`;
  } else {
    authStore.logout();
  }
}
</script>

<script>
import EmptyLayout from "@layouts/EmptyLayout.vue";

export default {
  computed: {
    layout() {
      return this.$route.meta.layout || EmptyLayout;
    }
  }
}
</script>

<template>
  <component :is="layout">
    <router-view/>
  </component>
</template>
