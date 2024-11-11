<script setup>
import {onMounted, reactive, ref} from "vue";
import {useRouter} from "vue-router";

import {useAuthStore} from "@stores";

import Logo from "@assets/images/logo.png";
import DarkModeSwitcher from "@components/DarkModeSwitcher.vue";

const {user} = useAuthStore();
const router = useRouter();
const login = ref('');
const password = ref('');
const error = reactive({value: ''});

const handleSubmit = async () => {
  error.value = '';

  const authStore = useAuthStore();
  const result = await authStore.login(login.value, password.value);

  if(result){
    error.value = result;
  }
};

onMounted(() => {
  if(user){
    router.push('/Admin/');
  }
});
</script>

<template>
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1600 800" class="fixed left-0 top-0 min-h-screen min-w-screen"
       preserveAspectRatio="none">
    <rect fill="var(--p-primary-950)" width="1600" height="800"></rect>
    <path fill="var(--p-primary-700)" d="M478.4 581c3.2 0.8 6.4 1.7 9.5 2.5c196.2 52.5 388.7 133.5 593.5 176.6c174.2 36.6 349.5 29.2 518.6-10.2V0H0v574.9c52.3-17.6 106.5-27.7 161.1-30.9C268.4 537.4 375.7 554.2
        478.4 581z"></path>
    <path fill="var(--p-primary-800)"
          d="M181.8 259.4c98.2 6 191.9 35.2 281.3 72.1c2.8 1.1 5.5 2.3 8.3 3.4c171 71.6 342.7 158.5 531.3 207.7c198.8 51.8 403.4 40.8 597.3-14.8V0H0v283.2C59 263.6 120.6 255.7 181.8 259.4z"></path>
    <path fill="var(--p-primary-700)"
          d="M454.9 86.3C600.7 177 751.6 269.3 924.1 325c208.6 67.4 431.3 60.8 637.9-5.3c12.8-4.1 25.4-8.4 38.1-12.9V0H288.1c56 21.3 108.7 50.6 159.7 82C450.2 83.4 452.5 84.9 454.9 86.3z"></path>
    <path fill="var(--p-primary-600)"
          d="M1397.5 154.8c47.2-10.6 93.6-25.3 138.6-43.8c21.7-8.9 43-18.8 63.9-29.5V0H643.4c62.9 41.7 129.7 78.2 202.1 107.4C1020.4 178.1 1214.2 196.1 1397.5 154.8z"></path>
  </svg>
  <div class="absolute z-50 top-0 right-0 flex items-end justify-center pr-2 pt-2">
    <DarkModeSwitcher />
  </div>

  <div class="absolute top-0 left-0 right-0 bottom-0">
    <div class="flex min-h-screen items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
      <!--      <RouterLink to="/" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">-->
      <!--        <img class="w-24 h-24 mr-2" :src="Logo" alt="logo">-->
      <!--        <div>-->
      <!--          <span class="block self-center text-2xl font-semibold whitespace-nowrap text-gray-800 dark:text-white">БИ КАРС</span>-->
      <!--          <span class="block self-center text-sm font-semibold whitespace-nowrap text-gray-300 dark:text-gray-500">АВТОПРОКАТ</span>-->
      <!--        </div>-->
      <!--      </RouterLink>-->
      <div
          class="w-full min-w-72 bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
        <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
          <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
            Войти в аккаунт
          </h1>
          <form @submit.prevent="handleSubmit">
            <label for="email1" class="block text-900 font-medium mb-2">Email</label>
            <InputText id="email1" type="text" placeholder="Введите email" class="w-full mb-5" v-model="login" inputClass="w-full"/>

            <label for="password1" class="block text-900 font-medium mb-2">Пароль</label>
            <Password id="password1" v-model="password" placeholder="Введите пароль" :toggleMask="true" :feedback="false" class="w-full mb-3" inputClass="w-full"></Password>

            <span v-if="error" class="text-red-400">{{ error.value }}</span>
            <button
                type="submit"
                class="w-full mt-4 text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
            >
              Войти
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>