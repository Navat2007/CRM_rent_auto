<script setup>
import {useForm} from 'vue-hooks-form'
import {onMounted, ref} from "vue";
import MultiSelect from "primevue/multiselect";
import CompanyService from "@services/CompanyService.js";

const loading = ref(true);
const companies = ref([]);

const {useField, validateFields} = useForm({
  defaultValues: {
    login: '',
    password: '',
    passwordConfirm: '',
    active: true
  },
  validateMode: 'submit'
});

const emit = defineEmits(['onSubmit']);

const email = useField('email', {
  rule: {
    required: true,
    validator: (rule, value) => {
      if (!/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/.test(value)) {
        return new Error('Некорректная почта!')
      }

      return true
    },
  },
});
const password = useField('password', {
  rule: {
    required: true,
    validator: (rule, value) => {
      if (value.length < 6) {
        return new Error('Пароль должен быть не менее 6 символов!')
      }

      return true
    },
  },
});
const passwordConfirm = useField('passwordConfirm', {
  rule: {
    required: true,
    validator: (rule, value) => {
      if (value.length < 6) {
        return new Error('Пароль должен быть не менее 6 символов!')
      }

      if (value !== password.value) {
        return new Error('Пароли должны совпадать!')
      }

      return true
    },
  },
});
const active = useField('active');
const selected_companies = ref();

const onSubmit = async (e) => {
  e.preventDefault();

  const errors = await validateFields();

  if (!errors) {
    const data = {
      email: email.value,
      password: password.value,
      active: active.value,
      companies: selected_companies.value
    };
    emit('onSubmit', data);
  }
}
const loadData = async () => {
  CompanyService.getCompanies(loading).then(data => {
    companies.value = data
  });
}

onMounted(() => {
  loadData();
});
</script>

<template>
  <form @submit.prevent="onSubmit">
    <div class="grid gap-4 mb-4 sm:grid-cols-1">
      <div>
        <label for="email"
               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Компания</label>
        <MultiSelect v-if="loading" placeholder="Loading..." loading class="block w-full"/>
        <MultiSelect
            v-if="!loading" v-model="selected_companies"
            display="chip" filter :options="companies" optionLabel="name" placeholder="Выберите компании"
            panel-style="z-index: 9999"
            class="block w-full"
        />
      </div>
      <div>
        <label for="email"
               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
        <input
            v-model="email.value" :ref="email.ref"
            type="text" id="email"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
            placeholder="..." required="" autocomplete="email"
        >
        <span v-if="email.error" class="text-red-500">{{ email.error.message }}</span>
      </div>
      <div>
        <label for="password"
               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Пароль</label>
        <input
            v-model="password.value" :ref="password.ref"
            type="password" id="password"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
            placeholder="..." required="" autocomplete="new-password"
        >
        <span v-if="password.error" class="text-red-500">{{ password.error.message }}</span>
      </div>
      <div>
        <label for="passwordConfirm"
               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Подтвердите пароль</label>
        <input
            v-model="passwordConfirm.value" :ref="passwordConfirm.ref"
            type="password" id="passwordConfirm"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
            placeholder="..." required="" autocomplete="new-password"
        >
        <span v-if="passwordConfirm.error" class="text-red-500">{{ passwordConfirm.error.message }}</span>
      </div>
      <div class="flex items-center">
        <input id="active" type="checkbox"
               v-model="active.value" :ref="active.ref"
               class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"/>

        <label for="active" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
          Активен?
        </label>
      </div>
    </div>
    <button type="submit"
            class="text-white inline-flex items-center bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
      <svg class="mr-1 -ml-1 w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd"
              d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
              clip-rule="evenodd"></path>
      </svg>
      Добавить
    </button>
  </form>
</template>