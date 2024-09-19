<script setup>
import {computed, reactive, ref, unref, onMounted} from "vue";
import {useAuthStore} from "@stores";
import {useVuelidate} from '@vuelidate/core';
import moment from "moment";
import {email, helpers, required, minLength, sameAs} from '@vuelidate/validators';
import Divider from "primevue/divider";
import FormError from "@components/Inputs/FormError.vue";
import DirectoryService from "@services/DirectoryService.js";
import DatePickerWithMask from "@components/Inputs/DatePickerWithMask.vue";
import AvatarSelect from "@components/Inputs/AvatarSelect.vue";
import TableWithRowEditing from "@components/Table/TableWithRowEditing.vue";
import FileGallery from "@components/Inputs/FileGallery.vue";

const {user} = useAuthStore();

const props = defineProps({
  sending: {
    type: Boolean,
    required: false,
    default: false
  },
  item: {
    type: Object,
    required: true
  }
});
const emit = defineEmits(['onSubmit', 'onArchive', 'onDelete']);

const genders = ref([
  {label: 'Мужской', value: 0,},
  {label: 'Женский', value: 1},
]);
const positions = ref([]);
const loadingPositions = ref(true);
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
const passwordDisabled = ref(true);
const access = ref([
  {name: 'Нет доступа', key: 0},
  {name: 'Просмотр', key: 1},
  {name: 'Редактирование', key: 2},
]);

const lettersAndDash = helpers.regex(/^[a-zA-Zа-яА-Я-]*$/);

const state = reactive({
  archive: parseInt(props.item.archive),
  companyId: user.company_id,
  email: props.item.email,
  firstName: props.item.first_name,
  lastName: props.item.second_name,
  patronym: props.item.middle_name,
  password: '',
  confirmPassword: '',
  birthday: props.item.birth_date ? moment(props.item.birth_date).format('DD.MM.YYYY') : null,
  gender: props.item.gender ? parseInt(props.item.gender) : 0,
  phone: props.item.phone,
  position: props.item.user_type ? parseInt(props.item.user_type) : 0,
  snils: props.item.snils,
  inn: props.item.inn,
  hireDate: props.item.hire_date ? moment(props.item.hire_date).format('DD.MM.YYYY') : null,
  actsOnBasis: props.item.acts_on_basis,
  rate: props.item.rate,
  active: parseInt(props.item.status) === 1,
  avatar: props.item.user_photo_avatar,
  contacts: props.item.contacts || [],
  note1: props.item.user_note_1,
  note2: props.item.user_note_2,
  note3: props.item.user_note_3,
  firingDate: props.item.firing_date ? moment(props.item.firing_date).format('DD.MM.YYYY') : null,
  passport_series_number: props.item.passport_series_number,
  passport_department_code: props.item.passport_department_code,
  passport_issued_by: props.item.passport_issued_by,
  passport_date_of_issue: props.item.passport_date_of_issue ? moment(props.item.passport_date_of_issue).format('DD.MM.YYYY') : null,
  passport_born_place: props.item.passport_born_place,
  passport_registration_address: props.item.passport_registration_address,
  passport_fact_address: props.item.passport_fact_address,
  passport_files: props.item.passport_files || [],
  passport_upload_files: [],
  dl_series_number: props.item.dl_series_number,
  dl_issued_by_who: props.item.dl_issued_by_who,
  dl_issued_date: props.item.dl_issued_date ? moment(props.item.dl_issued_date).format('DD.MM.YYYY') : null,
  dl_expire_date: props.item.dl_expire_date ? moment(props.item.dl_expire_date).format('DD.MM.YYYY') : null,
  dl_files: props.item.dl_files || [],
  dl_upload_files: [],
  other_files: props.item.other_files || [],
  other_upload_files: [],
  access_directory: parseInt(props.item.access_directory) || 0,
  access_employers: parseInt(props.item.access_employers) || 0,
  access_clients: parseInt(props.item.access_clients) || 0,
});
const rules = computed(() => {
  return {
    email: {
      required: helpers.withMessage("Email должен быть заполнен", required),
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
    password: {
      minLength: helpers.withMessage("Минимальная длина пароля - 6 символов", minLength(6)),
      $lazy: true
    },
    confirmPassword: {
      sameAsPassword: helpers.withMessage("Пароли не совпадают", sameAs(state.password)),
      $lazy: true
    },
    birthday: {},
    hireDate: {},
    actsOnBasis: {},
    rate: {},
  }
});
const v$ = useVuelidate(rules, state);

const onFormSubmit = async (e) => {
  const isFormCorrect = await unref(v$).$validate();

  if (isFormCorrect) {
    emit('onSubmit', state);
  }
};

const handleAddPositionButtonClick = () => {

};

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
    <template #title>Редактирование сотрудника <Badge v-if="state.archive === 1" value="Архив"></Badge></template>
    <template #content>
      <Tabs value="0" scrollable>
        <TabList>
          <Tab value="0" class="flex gap-2">Основная информация</Tab>
          <Tab value="1" class="flex gap-2">Паспорт</Tab>
          <Tab value="2" class="flex gap-2">Водительское удостоверение</Tab>
          <Tab value="3" class="flex gap-2">Прочие документы</Tab>
          <Tab value="4" class="flex gap-2">Права доступа</Tab>
        </TabList>
        <form @submit.prevent="onFormSubmit" autocomplete="off">
          <TabPanels>
            <TabPanel value="0">
              <div class="grid gap-4 sm:grid-cols-1">
                <!-- Avatar -->
                <div>
                  <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Аватарка</label>
                  <AvatarSelect :value="state.avatar" @onSelect="e => state.avatar = e.files[0]"
                                @onDelete="state.avatar = null"/>
                </div>
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
                    <DatePickerWithMask :value="state.birthday" @onChange="e => state.birthday = e"/>
                    <p class="mt-2">{{ age }}{{ zodiac }}</p>
                  </div>
                </div>
                <!-- Пол -->
                <div>
                  <label for="gender"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Пол</label>
                  <Select v-model="state.gender" :options="genders" :modelValue="state.gender" optionLabel="label"
                          optionValue="value" placeholder="Выберите пол" class="w-full"/>
                </div>
                <!-- Основной телефон -->
                <div>
                  <label for="phone"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Основной телефон</label>
                  <InputMask id="phone" v-model="state.phone" mask="(999) 999-9999" placeholder="(999) 999-9999" fluid/>
                </div>
                <!-- Дополнительные контакты -->
                <div>
                  <TableWithRowEditing
                      title="Дополнительные контакты"
                      :columns="[
                        { title: 'Контакт', value: 'contact', required: true },
                        { title: 'Имя', value: 'name' },
                        { title: 'Кем приходится', value: 'who' }
                      ]"
                      :items="state.contacts"
                      @onChange="e => state.contacts = e"
                  />
                </div>
                <!-- Должность -->
                <div>
                  <label for="position"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Должность</label>
                  <Select v-model="state.position" :loading="loadingPositions" :options="positions" optionLabel="name"
                          optionValue="id" placeholder="Выберите должность" showClear filter class="w-full">
                    <template v-if="user.access.directory === 2" #header>
                      <Button class="mt-4 ml-4" type="button" icon="pi pi-plus" label="Добавить" outlined @click="handleAddPositionButtonClick" />
                    </template>
                  </Select>
                </div>
                <!-- СНИЛС -->
                <div>
                  <label for="snils"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">СНИЛС</label>
                  <InputMask id="snils" v-model="state.snils" :modelValue="state.snils" mask="999-999-999 99"
                             placeholder="999-999-999 99" fluid/>
                </div>
                <!-- ИНН -->
                <div>
                  <label for="inn"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ИНН</label>
                  <InputMask id="inn" v-model="state.inn" :modelValue="state.inn" mask="999999999999"
                             placeholder="999999999999" fluid/>
                </div>
                <!-- Дата найма -->
                <div>
                  <label for="hireDate"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Дата найма</label>
                  <DatePickerWithMask :value="state.hireDate" @onChange="e => state.hireDate = e"/>
                </div>
                <!-- Действует на основании -->
                <div>
                  <label for="actsOnBasis"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Действует на
                    основании</label>
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
                <!-- Примечание 1 -->
                <div>
                  <label for="note1"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Примечание 1</label>
                  <input
                      v-model="state.note1"
                      type="text" id="note1"
                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                      placeholder="..."
                  >
                </div>
                <!-- Примечание 2 -->
                <div>
                  <label for="note2"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Примечание 2</label>
                  <input
                      v-model="state.note2"
                      type="text" id="note2"
                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                      placeholder="..."
                  >
                </div>
                <!-- Примечание 3 -->
                <div>
                  <label for="note3"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Примечание 3</label>
                  <input
                      v-model="state.note3"
                      type="text" id="note3"
                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                      placeholder="..."
                  >
                </div>
                <!-- Дата увольнения -->
                <div>
                  <label for="firingDate"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Дата увольнения</label>
                  <DatePickerWithMask :value="state.firingDate" @onChange="e => state.firingDate = e"/>
                </div>
                <div v-if="state.archive === 0" class="grid gap-4 my-4 sm:grid-cols-1">
                  <Divider type="dashed"/>
                  <div v-if="passwordDisabled">
                    <Button icon="pi pi-lock" label="Изменить пароль" severity="danger"
                            @click="passwordDisabled = false"/>
                  </div>
                  <!-- Пароль -->
                  <div>
                    <label for="password"
                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Новый пароль</label>
                    <Password inputId="password" v-model="state.password" placeholder="Введите пароль"
                              :toggleMask="true"
                              :feedback="false" class="w-full mb-3" inputClass="w-full" :disabled="passwordDisabled"/>
                    <FormError :errors="v$.password.$errors"/>
                  </div>
                  <!-- Подтвердить пароль -->
                  <div>
                    <label for="confirmPassword"
                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Подтвердить
                      пароль</label>
                    <Password inputId="confirmPassword" v-model="state.confirmPassword" placeholder="Введите пароль"
                              :toggleMask="true"
                              :feedback="false" class="w-full mb-3" inputClass="w-full" :disabled="passwordDisabled"/>
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
              </div>
            </TabPanel>
            <TabPanel value="1">
              <div class="grid gap-4 sm:grid-cols-1">
                <!-- Паспорт. Серия и номер -->
                <div>
                  <label for="passport_series_number"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Серия и номер</label>
                  <input
                      v-model="state.passport_series_number"
                      type="text" id="passport_series_number"
                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                      placeholder="..."
                  >
                </div>
                <!-- Паспорт. Код подразделения -->
                <div>
                  <label for="passport_department_code"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Код подразделения</label>
                  <input
                      v-model="state.passport_department_code"
                      type="text" id="passport_department_code"
                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                      placeholder="..."
                  >
                </div>
                <!-- Паспорт. Кем выдан -->
                <div>
                  <label for="passport_issued_by"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Кем выдан</label>
                  <input
                      v-model="state.passport_issued_by"
                      type="text" id="passport_issued_by"
                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                      placeholder="..."
                  >
                </div>
                <!-- Паспорт. Дата выдачи -->
                <div>
                  <label for="passport_date_of_issue"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Дата выдачи</label>
                  <DatePickerWithMask :value="state.passport_date_of_issue"
                                      @onChange="e => state.passport_date_of_issue = e"/>
                </div>
                <!-- Паспорт. Кем выдан -->
                <div>
                  <label for="passport_born_place"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Место рождения</label>
                  <input
                      v-model="state.passport_born_place"
                      type="text" id="passport_born_place"
                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                      placeholder="..."
                  >
                </div>
                <!-- Паспорт. Адрес регистрации -->
                <div>
                  <label for="passport_registration_address"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Адрес регистрации</label>
                  <input
                      v-model="state.passport_registration_address"
                      type="text" id="passport_registration_address"
                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                      placeholder="..."
                  >
                </div>
                <!-- Паспорт. Адрес фактический -->
                <div>
                  <label for="passport_fact_address"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Адрес фактический</label>
                  <input
                      v-model="state.passport_fact_address"
                      type="text" id="passport_fact_address"
                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                      placeholder="..."
                  >
                </div>
                <Divider type="dashed"/>
                <!-- Паспорт. Файлы -->
                <FileGallery :items="state.passport_files"
                             @onSelect="e => state.passport_upload_files = e.files"
                />
              </div>
            </TabPanel>
            <TabPanel value="2">
              <div class="grid gap-4 sm:grid-cols-1">
                <!-- Водительское удостоверение. Серия и номер -->
                <div>
                  <label for="dl_series_number"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Серия и номер</label>
                  <input
                      v-model="state.dl_series_number"
                      type="text" id="dl_series_number"
                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                      placeholder="..."
                  >
                </div>
                <!-- Водительское удостоверение. Кем выдан -->
                <div>
                  <label for="dl_issued_by_who"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Кем выдан</label>
                  <input
                      v-model="state.dl_issued_by_who"
                      type="text" id="dl_issued_by_who"
                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                      placeholder="..."
                  >
                </div>
                <!-- Водительское удостоверение. Дата выдачи -->
                <div>
                  <label for="dl_issued_date"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Дата выдачи</label>
                  <DatePickerWithMask :value="state.dl_issued_date" @onChange="e => state.dl_issued_date = e"/>
                </div>
                <!-- Водительское удостоверение. Действуют до -->
                <div>
                  <label for="dl_expire_date"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Действуют до</label>
                  <DatePickerWithMask :value="state.dl_expire_date" @onChange="e => state.dl_expire_date = e"/>
                </div>
                <Divider type="dashed"/>
                <!-- Водительское удостоверение. Файлы -->
                <FileGallery :items="state.dl_files" @onSelect="e => state.dl_upload_files = e.files"/>
              </div>
            </TabPanel>
            <TabPanel value="3">
              <!-- Прочие файлы -->
              <FileGallery :items="state.other_files" @onSelect="e => state.other_upload_files = e.files"/>
            </TabPanel>
            <TabPanel value="4">
              <Fieldset legend="Управление справочниками">
                <div v-for="item in access" :key="'access_directory' + item.key" class="flex items-center">
                  <RadioButton v-model="state.access_directory" :inputId="'access_directory' + item.key" name="dynamic"
                               :value="item.key"/>
                  <label :for="item.key" class="ml-2">{{ item.name }}</label>
                </div>
              </Fieldset>
              <Fieldset legend="Управление сотрудниками">
                <div v-for="item in access" :key="'access_employers' + item.key" class="flex items-center">
                  <RadioButton v-model="state.access_employers" :inputId="'access_employers' + item.key" name="dynamic"
                               :value="item.key"/>
                  <label :for="item.key" class="ml-2">{{ item.name }}</label>
                </div>
              </Fieldset>
              <Fieldset legend="Управление клиентами">
                <div v-for="item in access" :key="'access_clients' + item.key" class="flex items-center">
                  <RadioButton v-model="state.access_clients" :inputId="'access_clients' + item.key" name="dynamic"
                               :value="item.key"/>
                  <label :for="item.key" class="ml-2">{{ item.name }}</label>
                </div>
              </Fieldset>
            </TabPanel>
          </TabPanels>
          <Divider v-if="user.access.employers === 2" type="dashed"/>
          <p v-for="error of v$.$errors" :key="error.$uid" class="text-red-500">
            {{ error.$message }}
          </p>
          <div v-if="user.access.employers === 2 && state.archive === 0">
            <Button type="submit" icon="pi pi-save" label="Сохранить" :loading="sending" outlined/>
            <Button icon="pi pi-trash" label="В архив" class="ml-4" severity="secondary" :loading="sending"
                    @click="emit('onArchive');" outlined/>
            <Button v-if="user.id === 1" icon="pi pi-trash" label="Удалить" class="ml-4" severity="danger" :loading="sending"
                    @click="emit('onDelete');" outlined/>
          </div>
          <div v-if="state.archive === 1">
            <Tag severity="warn" value="Сотрудник находится в архиве."/>
          </div>
        </form>
      </Tabs>
    </template>
  </Card>
</template>