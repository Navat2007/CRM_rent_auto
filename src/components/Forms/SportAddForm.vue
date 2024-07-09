<script setup>
import {useForm} from 'vue-hooks-form'

const {useField, validateFields} = useForm({
  defaultValues: {
    ID: '',
    title: '',
    category: true,
    training: true,
    coach: true
  },
  validateMode: 'submit'
});

const emit = defineEmits(['onSubmit']);

const ID = useField('ID', {
  rule: {required: true},
});
const title = useField('title', {
  rule: {required: true},
});
const category = useField('category');
const training = useField('training');
const coach = useField('coach');

const onSubmit = async (e) => {
  e.preventDefault();

  const errors = await validateFields();

  if(!errors){
    const data = {
      sportID: ID.value,
      title: title.value,
      category: category.value ? 1 : 0,
      training: training.value ? 1 : 0,
      coach: coach.value ? 1 : 0
    };
    emit('onSubmit', data);
  }
}
</script>

<template>
  <form @submit.prevent="onSubmit">
    <div class="grid gap-4 mb-4 sm:grid-cols-2">
      <div>
        <label for="ID" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ID вида спорта</label>
        <input
            v-model="ID.value" :ref="ID.ref"
            type="number" min="0" id="ID"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
            placeholder="..." required=""
        >
        <span v-if="ID.error" class="text-red-500">{{ID.error.message}}</span>
      </div>
      <div>
        <label for="title"
               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Название вида спорта</label>
        <input
            v-model="title.value" :ref="title.ref"
            type="text" id="title"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
            placeholder="..." required=""
        >
        <span v-if="title.error" class="text-red-500">{{title.error.message}}</span>
      </div>
      <fieldset>
        <div class="flex items-center mb-4">
          <input v-model="category.value" :ref="category.ref" id="category" checked type="checkbox"
                 class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
          <label for="category"
                 class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Раздел категорий</label>
        </div>

        <div class="flex items-center mb-4">
          <input v-model="training.value" :ref="training.ref" id="training" checked type="checkbox"
                 class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
          <label for="training"
                 class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Раздел сборных</label>
        </div>

        <div class="flex items-center mb-4">
          <input v-model="coach.value" :ref="coach.ref" id="coach" checked type="checkbox"
                 class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
          <label for="coach"
                 class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Раздел тренеров</label>
        </div>
      </fieldset>
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