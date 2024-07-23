<script setup>
import {useForm} from 'vue-hooks-form'
import {onMounted, ref} from "vue";
import {initFlowbite} from 'flowbite'
import CompanyService from "@services/CompanyService.js";

import Divider from "primevue/divider";
import AlertModal from "@components/Modals/AlertModal.vue";
import router from "@router";

const isAlertModalOpen = ref(false);
const isSuccessModalOpen = ref(false);
const error = ref('');

const {useField, validateFields} = useForm({
  defaultValues: {
    login: '',
    password: '',
    passwordConfirm: '',
    active: true
  },
  validateMode: 'submit'
});

const name = useField('name', {
  rule: {
    required: true,
    validator: (rule, value) => {
      if (value.length < 3) {
        return new Error('Название должно быть не менее 3 символов!')
      }

      return true
    },
  },
});

const onSubmit = async (e) => {
  e.preventDefault();

  const errors = await validateFields();

  if (!errors) {
    const data = {
      name: name.value,
    };

    CompanyService.addCompany(data).then((response) => {
      console.log(response.data);
      if (response.data) {
        if (parseInt(response.data.error) === 0) {
          isSuccessModalOpen.value = true
        } else {
          error.value = response.data.error_text
          isAlertModalOpen.value = true
        }
      }
    });
  }
}
const onSuccess = () => {
  isSuccessModalOpen.value = false;
  router.push({name: 'AdminCompanies'});
}

onMounted(() => {
  //initFlowbite();
})
</script>

<template>
  <section class="">
    <div class="max-w-2xl px-8 py-8">
      <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
          <li class="inline-flex items-center">
            <a href="#" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
              <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
              </svg>
              Home
            </a>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
              </svg>
              <a href="#" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">Projects</a>
            </div>
          </li>
          <li aria-current="page">
            <div class="flex items-center">
              <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
              </svg>
              <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Flowbite</span>
            </div>
          </li>
        </ol>
      </nav>
      <h2 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Новая компания</h2>
      <form @submit.prevent="onSubmit">
        <div class="grid gap-4 mb-4 sm:grid-cols-2 py-8">
          <div>
            <label for="name"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Название</label>
            <input
                v-model="name.value" :ref="name.ref"
                type="text" id="name"
                class="name-input"
                placeholder="..." required=""
            >
            <span v-if="name.error" class="text-red-500">{{ name.error.message }}</span>
          </div>
        </div>
        <Divider type="dashed"/>
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
    </div>
  </section>

  <AlertModal :isOpen="isSuccessModalOpen" @close="onSuccess" title="Запрос выполнен" accept/>
  <AlertModal :isOpen="isAlertModalOpen" @close="isAlertModalOpen = false" :title="error" info/>
</template>

<style scoped>
.name-input {
  @apply bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500
}
</style>