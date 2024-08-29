<script setup>
import {useForm} from 'vue-hooks-form'
import {onMounted, ref} from "vue";
import AlertModal from "../../Modals/AlertModal.vue";

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

const props = defineProps({
  item: {
    type: Object,
    required: true
  }
})
const emit = defineEmits(['onSubmit', 'onDelete']);
const isDeleteModalOpen = ref(false);

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

  if (!errors) {
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
const handleDeleteBtn = (e) => {
  e.preventDefault();
  isDeleteModalOpen.value = true
}
const handleDeleteAccept = () => {
  const data = {
    ID: props.item.ID
  };
  emit('onDelete', data);
}

onMounted(() => {
  login.value = props.item.login;
  password.value = props.item.password;
  passwordConfirm.value = props.item.password;
})
</script>

<template>
  <form @submit.prevent="onSubmit">
    <div class="grid gap-4 mb-4 sm:grid-cols-2">
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
    <div class="flex items-center space-x-4">
      <button type="submit"
              class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
        Редактировать
      </button>
      <button @click="handleDeleteBtn"
              class="text-red-600 inline-flex items-center hover:text-white border border-red-600 hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900">
        <svg class="mr-1 -ml-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd"
                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                clip-rule="evenodd"></path>
        </svg>
        Удалить
      </button>
    </div>
  </form>
  <AlertModal
      target="#modal2"
      :isOpen="isDeleteModalOpen"
      @close="isDeleteModalOpen = false"
      @accept="handleDeleteAccept"
      title="Вы действительно хотите удалить?"
      withButtons info/>
</template>