<script setup>
import { onClickOutside } from '@vueuse/core'
import {computed, ref} from 'vue'
import {useAuthStore} from "@stores";

import noPhoto from '@assets/images/no_photo_man.png'
import AlertModal from "@components/Modals/AlertModal.vue";
import router from "@router";

const authStore = useAuthStore();
const target = ref(null)
const dropdownOpen = ref(false)
const isLogoutModalOpen = ref(false);
const photo = computed(() => {
  let tempPhoto = noPhoto;

  if(authStore.user.photo && authStore.user.photo !== ""){
    tempPhoto = import.meta.env.VITE_FILE_URL + "/" + authStore.user.photo;
  }

  return tempPhoto;
});

onClickOutside(target, () => {
  dropdownOpen.value = false
})

const handleProfileClick = () => {
  dropdownOpen.value = false
  router.push('/Admin/profile')
}
const handleLogout = async () => {
  await authStore.logout();
}
</script>

<template>
  <div class="relative" ref="target">
    <router-link
      class="flex items-center gap-4"
      to="#"
      @click.prevent="dropdownOpen = !dropdownOpen"
    >
      <span class="hidden text-right lg:block">
        <span class="block text-sm font-medium text-black dark:text-white">{{authStore.user.email}}</span>
        <span class="block text-xs font-medium">{{authStore.user.role_title}}</span>
      </span>

      <Avatar :image="photo" size="large" shape="circle"/>

      <svg
        :class="dropdownOpen && 'rotate-180'"
        class="hidden fill-current sm:block"
        width="12"
        height="8"
        viewBox="0 0 12 8"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          fill-rule="evenodd"
          clip-rule="evenodd"
          d="M0.410765 0.910734C0.736202 0.585297 1.26384 0.585297 1.58928 0.910734L6.00002 5.32148L10.4108 0.910734C10.7362 0.585297 11.2638 0.585297 11.5893 0.910734C11.9147 1.23617 11.9147 1.76381 11.5893 2.08924L6.58928 7.08924C6.26384 7.41468 5.7362 7.41468 5.41077 7.08924L0.410765 2.08924C0.0853277 1.76381 0.0853277 1.23617 0.410765 0.910734Z"
          fill=""
        />
      </svg>
    </router-link>

    <!-- Dropdown Start -->
    <div
      v-show="dropdownOpen"
      class="absolute right-0 mt-4 flex w-62.5 flex-col p-2 gap-2 rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark"
    >
      <Button icon="pi pi-user" label="Профиль" class="" severity="secondary"
              @click="handleProfileClick" outlined/>
      <Button icon="pi pi-sign-out" label="Выйти" class="" severity="danger"
              @click="isLogoutModalOpen = true" outlined/>
    </div>
    <!-- Dropdown End -->
  </div>

  <AlertModal
      target="#modal"
      :isOpen="isLogoutModalOpen"
      @close="isLogoutModalOpen = false"
      @accept="handleLogout"
      title="Вы действительно хотите выйти?"
      withButtons info
  />
</template>
