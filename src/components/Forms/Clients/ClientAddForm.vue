<script setup>
import {useAuthStore} from "@stores";
import {useVuelidate} from '@vuelidate/core';
import moment from "moment";
import {email, helpers, required, minLength, sameAs} from '@vuelidate/validators';
import {computed, reactive, ref, unref, onMounted} from "vue";
import Divider from "primevue/divider";
import FormError from "@components/Inputs/FormError.vue";
import DatePickerWithMask from "@components/Inputs/DatePickerWithMask.vue";
import DirectoryService from "@services/DirectoryService.js";
import LegalPersonsService from "@services/LegalPersonsService.js";
import PopUpAddAdvertisingType from "@components/Popups/PopUpAddAdvertisingType.vue";
import {lettersAndDash} from "@utils/formCheck.js";

const {user} = useAuthStore();

const props = defineProps({
  sending: {
    type: Boolean,
    required: false,
    default: false
  },
});
const emit = defineEmits(['onSubmit']);

const loadingAdvertising = ref(true);
const loadingLegalPersons = ref(true);
const advertising_type = ref([]);
const legalPersons = ref([]);
const isAdvertisingAddModalOpen = ref(false);
const genders = ref([
  {label: 'Мужской', value: 0,},
  {label: 'Женский', value: 1},
]);
const age = computed(() => {
  const declOfNum = (number, words) => {
    return words[(number % 100 > 4 && number % 100 < 20) ? 2 : [2, 0, 1, 1, 1, 2][(number % 10 < 5) ? Math.abs(number) % 10 : 5]];
  }

  const today = new Date();
  const birthDate = moment(state.birthday, 'DD.MM.YYYY').toDate();
  let age = today.getFullYear() - birthDate.getFullYear();

  if (today.getMonth() < birthDate.getMonth() ||
      (today.getMonth() === birthDate.getMonth() && today.getDate() < birthDate.getDate())) {
    age--;
  }

  return state.birthday ? age + ' ' + declOfNum(age, ['год', 'года', 'лет']) : null;
});
const zodiac = computed(() => {
  function getZodiacSign(date) {
    const month = date.getMonth() + 1;
    const day = date.getDate();

    if (month == 1 && day >= 20 || month == 2 && day <= 18) return "Водолей";
    else if (month == 2 && day >= 19 || month == 3 && day <= 20) return "Рыбы";
    else if (month == 3 && day >= 21 || month == 4 && day <= 19) return "Овен";
    else if (month == 4 && day >= 20 || month == 5 && day <= 20) return "Телец";
    else if (month == 5 && day >= 21 || month == 6 && day <= 21) return "Близнецы";
    else if (month == 6 && day >= 22 || month == 7 && day <= 22) return "Рак";
    else if (month == 7 && day >= 23 || month == 8 && day <= 22) return "Лев";
    else if (month == 8 && day >= 23 || month == 9 && day <= 22) return "Дева";
    else if (month == 9 && day >= 23 || month == 10 && day <= 22) return "Весы";
    else if (month == 10 && day >= 23 || month == 11 && day <= 21) return "Скорпион";
    else if (month == 11 && day >= 22 || month == 12 && day <= 21) return "Стрелец";
    else if (month == 12 && day >= 22 || month == 1 && day <= 19) return "Козерог";
  }

  const birthDate = moment(state.birthday, 'DD.MM.YYYY').toDate();
  return state.birthday ? ' (' + getZodiacSign(birthDate) + ')' : null;
});

const state = reactive({
  companyId: user.company_id,
  email: '',
  firstName: '',
  lastName: '',
  patronym: '',
  birthday: null,
  gender: 0,
  advertising: 0,
  legalPerson: 0,
  phone: '',
  snils: '',
  inn: '',
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
      required: helpers.withMessage("Имя должно быть заполнено", required),
      lettersAndDash: helpers.withMessage("Имя должно содержать только буквы и тире", lettersAndDash),
      $lazy: true
    },
    lastName: {
      required: helpers.withMessage("Фамилия должна быть заполнена", required),
      lettersAndDash: helpers.withMessage("Фамилия должна содержать только буквы и тире", lettersAndDash),
      $lazy: true
    },
    patronym: {
      lettersAndDash: helpers.withMessage("Отчество должно содержать только буквы и тире", lettersAndDash),
    },
    birthday: {},
  }
});
const v$ = useVuelidate(rules, state);

const onFormSubmit = async (e) => {
  const isFormCorrect = await unref(v$).$validate();

  if (isFormCorrect) {
    emit('onSubmit', state);
  }
};

const onAdvertisingAdd = async (id) => {
  loadingAdvertising.value = true;
  await fetchAdvertising();
  state.advertising = id;
}

async function fetchAdvertising() {
  advertising_type.value = (await DirectoryService.getAdvertisingTypes(user.company_id)).filter(advertising => advertising.archive === "Активен");
  loadingAdvertising.value = false;
}

async function fetchLegalPersons() {
  legalPersons.value = (await LegalPersonsService.getLegalPersons(user.company_id)).filter(person => person.status === "Активен");
  loadingLegalPersons.value = false;
}

onMounted(() => {
  fetchAdvertising();
  fetchLegalPersons();
});
</script>

<template>
  <Card class="w-full lg:w-2/3">
    <template #title>Добавление клиента</template>
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
            <div>
              <DatePickerWithMask :value="state.birthday" @onChange="e => state.birthday = e" />
              <p class="mt-2">{{ age }}{{ zodiac }}</p>
            </div>
            <FormError :errors="v$.birthday.$errors"/>
          </div>
          <!-- Пол -->
          <div>
            <label for="gender"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Пол</label>
            <Select v-model="state.gender" :options="genders" :modelValue="state.gender" optionLabel="label"
                    optionValue="value" placeholder="Выберите пол" class="w-full"/>
          </div>
          <!-- Телефон -->
          <div>
            <label for="phone"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Телефон</label>
            <InputMask id="phone" v-model="state.phone" mask="(999) 999-9999" placeholder="(999) 999-9999" fluid/>
          </div>
          <!-- СНИЛС -->
          <div>
            <label for="snils"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">СНИЛС</label>
            <InputMask id="snils" v-model="state.snils" mask="999-999-999 99" placeholder="999-999-999 99" fluid/>
          </div>
          <!-- ИНН -->
          <div>
            <label for="inn"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ИНН</label>
            <InputMask id="inn" v-model="state.inn" mask="999999999999" placeholder="999999999999" fluid/>
          </div>
          <Divider type="dashed"/>
          <!--  Вид рекламы -->
          <div>
            <label for="position"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Вид рекламы</label>
            <Select v-model="state.advertising" :loading="loadingAdvertising" :options="advertising_type" optionLabel="name"
                    optionValue="id" placeholder="Выберите вид рекламы" showClear filter class="w-full" >
              <template v-if="user.access.directory === 2" #header>
                <Button class="mt-4 ml-4" type="button" icon="pi pi-plus" label="Добавить" outlined @click="isAdvertisingAddModalOpen = true" />
              </template>
            </Select>
          </div>
          <!--  Организация клиента -->
          <div>
            <label for="position"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Организация клиента</label>
            <Select v-model="state.legalPerson" :loading="loadingLegalPersons" :options="legalPersons" optionLabel="full_name"
                    optionValue="id" placeholder="Выберите организацию клиента" showClear filter class="w-full" >
            </Select>
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
        <p v-for="error of v$.$errors" :key="error.$uid" class="text-red-500">
          {{ error.$message }}
        </p>
        <Button v-if="user.access.clients === 2" type="submit" icon="pi pi-plus" label="Добавить" outlined/>
      </form>
    </template>
  </Card>
  <PopUpAddAdvertisingType :visible="isAdvertisingAddModalOpen" @on-add="onAdvertisingAdd" @on-close="isAdvertisingAddModalOpen = false" />
</template>