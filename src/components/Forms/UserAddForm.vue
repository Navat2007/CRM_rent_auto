<script setup>
import {useForm} from 'vue-hooks-form'

const {useField, validateFields} = useForm({
  defaultValues: {
    login: '',
    password: '',
    passwordConfirm: ''
  },
  validateMode: 'submit'
});

const emit = defineEmits(['onSubmit']);

const login = useField('login', {
  rule: {required: true},
});
const password = useField('password', {
  rule: {required: true},
});
const passwordConfirm = useField('passwordConfirm', {
  rule: {required: true},
});

const onSubmit = async (e) => {
  e.preventDefault();

  const errors = await validateFields();

  if(!errors){
    const data = {
      login: login.value,
      password: password.value,
    };
    emit('onSubmit', data);
  }
}
</script>

<template>
  <form @submit.prevent="onSubmit">
    <div class="grid gap-4 mb-4 sm:grid-cols-1">
      <div>
        <label for="login"
               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Логин</label>
        <input
            v-model="login.value" :ref="login.ref"
            type="text" id="login"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
            placeholder="..." required=""
        >
        <span v-if="login.error" class="text-red-500">{{login.error.message}}</span>
      </div>
      <div>
        <label for="password"
               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Пароль</label>
        <input
            v-model="password.value" :ref="password.ref"
            type="password" id="password"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
            placeholder="..." required=""
        >
        <span v-if="password.error" class="text-red-500">{{password.error.message}}</span>
      </div>
      <div>
        <label for="passwordConfirm"
               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Подтвердите пароль</label>
        <input
            v-model="passwordConfirm.value" :ref="passwordConfirm.ref"
            type="password" id="passwordConfirm"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
            placeholder="..." required=""
        >
        <span v-if="passwordConfirm.error" class="text-red-500">{{passwordConfirm.error.message}}</span>
      </div>
    </div>
    <button type="submit"
            class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
      <svg class="mr-1 -ml-1 w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd"
              d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
              clip-rule="evenodd"></path>
      </svg>
      Добавить
    </button>
  </form>
</template>