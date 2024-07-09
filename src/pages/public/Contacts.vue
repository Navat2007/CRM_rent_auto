<script setup>
import {useForm} from 'vue-hooks-form';
import axios from "axios";
import AlertModal from "../../components/Modals/AlertModal.vue";
import {ref} from "vue";

const {useField, validateFields} = useForm({
  defaultValues: {
    email: '',
  },
  validateMode: 'submit'
});

const email = useField('email', {
  rule: {required: true, message: 'email не может быть пустым!'},
});
const subject = useField('subject', {
  rule: {required: true, message: 'Тема не может быть пустым!'},
});
const message = useField('message', {
  rule: {required: true, message: 'Сообщение не может быть пустым!'},
});

const isSuccessModalOpen = ref(false);
const isAlertModalOpen = ref(false);
const error = ref('');

const onSubmit = async (e) => {
  e.preventDefault();

  const errors = await validateFields();
  const url = `https://sova-games.ru/php/models/messages/add.php`;

  const formData = new FormData();
  formData.append('email', email.value);
  formData.append('subject', subject.value);
  formData.append('message', message.value);

  if (!errors) {
    axios.postForm(url, formData, {headers: {'Content-Type': 'application/json'}})
    .then((response) => {
      if (response.data && !response.data.error) {
        email.value = '';
        subject.value = '';
        message.value = '';

        isSuccessModalOpen.value = true
      } else {
        error.value = response.data.error_text;
        isAlertModalOpen.value = true
      }
    })
    .catch(err => {
      console.log(err);
    })
  }
};
</script>

<template>
  <div class="py-8 lg:py-16 px-4 mx-auto max-w-screen-md">
    <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-center text-gray-900 dark:text-white">Свяжитесь с нами</h2>
    <p class="mb-8 lg:mb-16 font-light text-center text-gray-500 dark:text-gray-400 sm:text-xl">Возникли вопросы или есть предложения? Напишите нам</p>
    <form @submit.prevent="onSubmit" class="space-y-8">
      <div>
        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Ваш email</label>
        <input v-model="email.value" :ref="email.ref" type="email" id="email"
               class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 dark:shadow-sm-light"
               placeholder="test@test.com" required>
      </div>
      <div>
        <label for="subject" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Тема</label>
        <input v-model="subject.value" :ref="subject.ref" type="text" id="subject"
               class="block p-3 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 dark:shadow-sm-light"
               placeholder="Чем мы можем помочь?" required>
      </div>
      <div class="sm:col-span-2">
        <label for="message"
               class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400">Ваше сообщение</label>
        <textarea v-model="message.value" :ref="message.ref" id="message" rows="6"
                  class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg shadow-sm border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                  placeholder="Опишите свой вопрос..."></textarea>
      </div>
      <button type="submit"
              class="py-3 px-5 text-sm font-medium text-center text-white rounded-lg bg-primary-700 sm:w-fit hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Отправить
      </button>
    </form>
  </div>

  <AlertModal :isOpen="isSuccessModalOpen" @close="isSuccessModalOpen = false" title="Сообщение успешно отправлено"
              accept/>
  <AlertModal :isOpen="isAlertModalOpen" @close="isAlertModalOpen = false" :title="error" info/>
</template>