<script setup>
import {RouterView} from 'vue-router';
import axios from "axios";
import {useAuthStore} from "@stores";

const authStore = useAuthStore();

if (authStore.user)
{
  axios.defaults.headers.post['Authorization'] = `${authStore.user.token}&${authStore.user.id}`;
}
</script>

<script>
export default {
  computed: {
    layout(){
      return this.$route.meta.layout || 'public'
    }
  }
}
</script>

<template>
  <component :is="layout">
    <router-view/>
  </component>
</template>
