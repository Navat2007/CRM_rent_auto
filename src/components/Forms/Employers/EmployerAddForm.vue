<script setup>
import {useAuthStore} from "@stores";
import {useVuelidate} from '@vuelidate/core';
import {email, helpers, required, minLength, sameAs} from '@vuelidate/validators';
import {computed, reactive, ref, unref, onMounted} from "vue";
import Divider from "primevue/divider";
import FormError from "@components/Inputs/FormError.vue";
import DirectoryService from "@services/DirectoryService.js";

const {user} = useAuthStore();

const props = defineProps({
  sending: {
    type: Boolean,
    required: false,
    default: false
  },
})
const emit = defineEmits(['onSubmit']);

const positions = ref([]);
const loadingPositions = ref(true);

const lettersAndDash = helpers.regex(/^[a-zA-Zа-яА-Я-]*$/)

const state = reactive({
  companyId: user.company_id,
  email: '',
  firstName: '',
  lastName: '',
  patronym: '',
  password: '',
  confirmPassword: '',
  birthday: null,
  gender: 0,
  phone: '',
  position: '',
  snils: '',
  inn: '',
  hireDate: null,
  actsOnBasis: '',
  rate: '',
  active: true,
});
const rules = computed(() => {
  return {
    email: {
      required: helpers.withMessage("Поле должно быть заполнено", required),
      email: helpers.withMessage("Введите корректный email", email),
      $lazy: true
    },
    firstName: {
      required: helpers.withMessage("Поле должно быть заполнено", required),
      lettersAndDash: helpers.withMessage("Только буквы и тире", lettersAndDash),
      $lazy: true
    },
    lastName: {
      required: helpers.withMessage("Поле должно быть заполнено", required),
      lettersAndDash: helpers.withMessage("Только буквы и тире", lettersAndDash),
      $lazy: true
    },
    patronym: {
      lettersAndDash: helpers.withMessage("Только буквы и тире", lettersAndDash),
    },
    password: {
      required: helpers.withMessage("Поле должно быть заполнено", required),
      minLength: helpers.withMessage("Минимальная длина пароля - 6 символов", minLength(6)),
      $lazy: true
    },
    confirmPassword: {
      required: helpers.withMessage("Поле должно быть заполнено", required),
      sameAsPassword: helpers.withMessage("Пароли не совпадают", sameAs(state.password)),
      $lazy: true
    },
    birthday: {},
    hireDate: {},
    actsOnBasis: {},
    rate: {},
  }
})
const v$ = useVuelidate(rules, state);

const genders = ref([
  { label: 'Мужской', value: 0, },
  { label: 'Женский', value: 1 },
]);

const onFormSubmit = async (e) => {
  const isFormCorrect = await unref(v$).$validate();

  if (isFormCorrect) {
    emit('onSubmit', state);
  }
}

async function fetchPositions() {
  positions.value = (await DirectoryService.getPositions(user.company_id)).filter(position => position.archive === "Активен");
  loadingPositions.value = false;
}

onMounted(() => {
  fetchPositions();
});
</script>

<template>
  <Card class="w-full lg:w-2/3">
    <template #title>Добавление сотрудника</template>
    <template #content>
      <form @submit.prevent="onFormSubmit">
        <div class="grid gap-4 my-4 sm:grid-cols-1">
          <!-- Email -->
          <div>
            <label for="email"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email*</label>
            <input
                v-model="state.email"
                type="email" id="email"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="..."
            >
            <FormError :errors="v$.email.$errors"/>
          </div>
          <!-- Фамилия -->
          <div>
            <label for="firstName"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Фамилия*</label>
            <input
                v-model="state.lastName"
                type="text" id="lastName"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="..."
            >
            <FormError :errors="v$.lastName.$errors"/>
          </div>
          <!-- Имя -->
          <div>
            <label for="firstName"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Имя*</label>
            <input
                v-model="state.firstName"
                type="text" id="firstName"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="..."
            >
            <FormError :errors="v$.firstName.$errors"/>
          </div>
          <!-- Отчество -->
          <div>
            <label for="patronym"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Отчество</label>
            <input
                v-model="state.patronym"
                type="text" id="patronym"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="..."
            >
            <FormError :errors="v$.patronym.$errors"/>
          </div>
          <Divider type="dashed"/>
          <!-- Дата рождения -->
          <div>
            <label for="birthday"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Дата рождения</label>
            <DatePicker v-model="state.birthday" id="birthday" dateFormat="dd.mm.yy" mask="99/99/9999" slotChar="dd.mm.yy" showIcon showButtonBar iconDisplay="input"/>
            <FormError :errors="v$.birthday.$errors"/>
          </div>
          <!-- Пол -->
          <div>
            <label for="gender"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Пол</label>
            <Select v-model="state.gender" :options="genders" :modelValue="state.gender" optionLabel="label" optionValue="value" placeholder="Выберите пол" class="w-full" />
          </div>
          <!-- Телефон -->
          <div>
            <label for="phone"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Телефон</label>
            <InputMask id="phone" v-model="state.phone" mask="(999) 999-9999" placeholder="(999) 999-9999" fluid />
          </div>
          <!-- Должность -->
          <div>
            <label for="position"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Должность</label>
            <Select v-model="state.position" :loading="loadingPositions" :options="positions" optionLabel="name" optionValue="id" placeholder="Выберите должность" showClear class="w-full" />
          </div>
          <!-- СНИЛС -->
          <div>
            <label for="snils"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">СНИЛС</label>
            <InputMask id="snils" v-model="state.snils" mask="999-999-999 99" placeholder="999-999-999 99" fluid />
          </div>
          <!-- ИНН -->
          <div>
            <label for="inn"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ИНН</label>
            <InputMask id="inn" v-model="state.inn" mask="999999999999" placeholder="999999999999" fluid />
          </div>
          <!-- Дата найма -->
          <div>
            <label for="hireDate"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Дата найма</label>
            <DatePicker v-model="state.hireDate" id="hireDate" dateFormat="dd.mm.yy" mask="99/99/9999" slotChar="dd.mm.yy" showIcon showButtonBar iconDisplay="input"/>
            <FormError :errors="v$.hireDate.$errors"/>
          </div>
          <!-- Действует на основании -->
          <div>
            <label for="actsOnBasis"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Действует на основании</label>
            <input
                v-model="state.actsOnBasis"
                type="text" id="actsOnBasis"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="..."
            >
            <FormError :errors="v$.actsOnBasis.$errors"/>
          </div>
          <!-- Ставка -->
          <div>
            <label for="rate"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ставка</label>
            <input
                v-model="state.rate"
                type="text" id="rate"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="..."
            >
            <FormError :errors="v$.rate.$errors"/>
          </div>
          <Divider type="dashed"/>
          <!-- Пароль -->
          <div>
            <label for="password"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Пароль*</label>
            <Password id="password" v-model="state.password" placeholder="Введите пароль" :toggleMask="true"
                      :feedback="false" class="w-full mb-3" inputClass="w-full"/>
            <FormError :errors="v$.password.$errors"/>
          </div>
          <!-- Подтвердить пароль -->
          <div>
            <label for="confirmPassword"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Подтвердить пароль*</label>
            <Password id="confirmPassword" v-model="state.confirmPassword" placeholder="Введите пароль" :toggleMask="true"
                      :feedback="false" class="w-full mb-3" inputClass="w-full"/>
            <FormError :errors="v$.confirmPassword.$errors"/>
          </div>
          <Divider type="dashed"/>
          <div class="flex items-center">
            <input id="active" type="checkbox"
                   v-model="state.active"
                   class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"/>
            <label for="active" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
              Активен?
            </label>
          </div>
        </div>
        <Divider type="dashed"/>
        <Button type="submit" icon="pi pi-plus" label="Добавить" class="main-button"/>
      </form>
    </template>
  </Card>
</template>