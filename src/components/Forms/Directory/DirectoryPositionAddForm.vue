<script setup>
import {useForm} from 'vue-hooks-form'
import {onMounted, ref} from "vue";
import Divider from "primevue/divider";

const {useField, validateFields} = useForm({
  defaultValues: {
    login: '',
    password: '',
    passwordConfirm: '',
    active: true
  },
  validateMode: 'submit'
});

const props = defineProps({
  sending: {
    type: Boolean,
    required: false,
    default: false
  },
})
const emit = defineEmits(['onSubmit']);

const name = useField('name', {
  rule: {
    required: true,
    validator: (rule, value) => {
      if (value.length < 1) {
        return new Error('Заполните данное поле!')
      }

      return true
    },
  },
});
const active = useField('active');

const onSubmit = async (e) => {
  e.preventDefault();

  const errors = await validateFields();

  if (!errors) {
    const data = {
      name: name.value,
      active: active.value,
    };
    emit('onSubmit', data);
  }
}
</script>

<template>
  <Card class="w-full lg:w-2/3">
    <template #title>Добавление должности</template>
    <template #content>
      <form @submit.prevent="onSubmit">
        <div class="grid gap-4 my-4 sm:grid-cols-1">
          <div>
            <label for="name"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Название</label>
            <input
                v-model="name.value" :ref="name.ref"
                type="text" id="name"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="..." required=""
            >
            <span v-if="name.error" class="text-red-500">{{ name.error.message }}</span>
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
        <Divider type="dashed"/>
        <Button v-if="user.access.directory === 2" type="submit" icon="pi pi-plus" label="Добавить" outlined />
      </form>
    </template>
  </Card>
</template>