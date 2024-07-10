<script setup>
import {reactive, ref} from "vue";
import {useRouter} from "vue-router";

import {useAuthStore} from "@stores";

import Logo from "@assets/images/logo.png";
import DarkToggle from "@components/DarkToggle.vue";

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
}
</script>

<template>
  <section class="h-screen w-screen bg-gray-50 dark:bg-gray-900">
    <div class="absolute top-0 right-0 flex items-end justify-center pr-2 pt-2">
      <DarkToggle />
    </div>

    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
      <RouterLink to="/" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
        <img class="w-24 h-24 mr-2" :src="Logo" alt="logo">
        <div>
          <span class="block self-center text-2xl font-semibold whitespace-nowrap text-gray-800 dark:text-white">БИ КАРС</span>
          <span class="block self-center text-sm font-semibold whitespace-nowrap text-gray-300 dark:text-gray-500">АВТОПРОКАТ</span>
        </div>
      </RouterLink>
      <div
          class="w-full min-w-72 bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
        <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
          <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
            Войти в аккаунт
          </h1>
          <form @submit.prevent="handleSubmit" class="space-y-4 md:space-y-6">
            <div>
              <label for="login" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Логин</label>
              <input v-model="login" type="text" name="login" id="login"
                     class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                     placeholder="name@company.com" required>
            </div>
            <div>
              <label for="password"
                     class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Пароль</label>
              <input v-model="password" type="password" name="password" id="password" placeholder="••••••••"
                     class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                     required>
            </div>
            <span v-if="error" class="text-red-400">{{ error.value }}</span>
            <div class="hidden flex items-center justify-between">
              <div class="flex items-start">
                <div class="flex items-center h-5">
                  <input v-model="remember" id="remember" aria-describedby="remember" type="checkbox"
                         class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-primary-600 dark:ring-offset-gray-800"
                  >
                </div>
                <div class="ml-3 text-sm">
                  <label for="remember" class="text-gray-500 dark:text-gray-300">Запомнить меня</label>
                </div>
              </div>
              <a class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-500">Забыли пароль?</a>
            </div>
            <button
                type="submit"
                class="w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
            >
              Войти
            </button>
            <p class="hidden text-sm font-light text-gray-500 dark:text-gray-400 flex items-center justify-between">
              <span>Нет аккаунта?</span>
              <a class="font-medium text-primary-600 hover:underline dark:text-primary-500">
                Зарегистрироваться
              </a>
            </p>
          </form>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>

</style>