<script setup>
import {computed, onMounted, reactive, ref, unref} from "vue";
import {useAuthStore} from "@stores";
import {useVuelidate} from '@vuelidate/core';
import moment from "moment";
import {email, helpers, required} from '@vuelidate/validators';
import Divider from "primevue/divider";
import FormError from "@components/Inputs/FormError.vue";
import DirectoryService from "@services/DirectoryService.js";
import DatePickerWithMask from "@components/Inputs/DatePickerWithMask.vue";
import AvatarSelect from "@components/Inputs/AvatarSelect.vue";
import TableWithRowEditing from "@components/Table/TableWithRowEditing.vue";
import FileGallery from "@components/Inputs/FileGallery.vue";
import LegalPersonsService from "@services/LegalPersonsService.js";
import PopUpAddAdvertisingType from "@components/Popups/PopUpAddAdvertisingType.vue";
import {lettersAndDash} from "@utils/formCheck.js";
import YandexOCR from "@components/Inputs/YandexOCR.vue";
import Odyssey from "@components/Inputs/Odyssey.vue";
import OdysseyService from "@services/OdysseyService.js";
import Table from "@components/Table/Table.vue";
import {FilterMatchMode, FilterOperator} from "@primevue/core/api";
import AlertModal from "@components/Modals/AlertModal.vue";

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

const loadingLegalPersons = ref(true);
const legalPersons = ref([]);

const loadingOdysseyResults = ref(true);
const isOdysseyResultModalOpen = ref(false);
const odysseyResult = ref({});
const odysseyResults = ref([]);
const odysseyColumns = ref([
  {
    header: 'ID',
    field: 'id',
  },
  {
    header: 'Дата',
    field: 'created_at',
  },
  {
    header: 'Показатель скоринга',
    field: 'scoring_overall_indicator',
  },
  {
    header: 'Фамилия',
    field: 'lastname',
  },
  {
    header: 'Имя',
    field: 'firstname',
  },
  {
    header: 'Отчество',
    field: 'middlename',
  },
  {
    header: 'Дата рождения',
    field: 'birthday',
  },
  {
    header: 'Полный отчет',
    field: 'url',
  },
]);
const odysseyFilters = ref({
  id: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
  scoring_overall_indicator: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
  birthday: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.DATE_IS}]},
  created_at: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.DATE_IS}]},
  lastname: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}]},
  firstname: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}]},
  middlename: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}]},
});
const odysseyFilterFields = ref(['id', 'created_at', 'lastname', 'firstname', 'middlename', 'birthday']);

const loadingAdvertising = ref(true);
const advertising_type = ref([]);
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
  archive: parseInt(props.item.archive),
  companyId: user.company_id,
  email: props.item.email,
  firstName: props.item.first_name,
  lastName: props.item.second_name,
  patronym: props.item.middle_name,
  birthday: props.item.birth_date ? moment(props.item.birth_date).format('DD.MM.YYYY') : null,
  gender: props.item.gender ? parseInt(props.item.gender) : 0,
  phone: props.item.phone,
  advertising: props.item.directory_advertising_type_id ? parseInt(props.item.directory_advertising_type_id) : 0,
  legalPerson: props.item.legal_person_id ? parseInt(props.item.legal_person_id) : 0,
  snils: props.item.snils,
  inn: props.item.inn,
  active: parseInt(props.item.status) === 1,
  negative: parseInt(props.item.negative) === 1,
  debtor: parseInt(props.item.debtor) === 1,
  verification_failure: parseInt(props.item.verification_failure) === 1,
  avatar: props.item.user_photo_avatar,
  contacts: props.item.contacts || [],
  note: props.item.note,
  note1: props.item.client_note_1,
  note2: props.item.client_note_2,
  note3: props.item.client_note_3,
  bank_card: props.item.bank_card,
  passport_series_number: props.item.passport_series_number,
  passport_department_code: props.item.passport_department_code,
  passport_issued_by: props.item.passport_issued_by,
  passport_date_of_issue: props.item.passport_date_of_issue ? moment(props.item.passport_date_of_issue).format('DD.MM.YYYY') : null,
  passport_born_place: props.item.passport_born_place,
  passport_registration_address: props.item.passport_registration_address,
  passport_fact_address: props.item.passport_fact_address,
  passport_files: props.item.passport_files || [],
  passport_upload_files: [],
  passport_ocr_upload_files: [],
  dl_series_number: props.item.dl_series_number,
  dl_issued_by_who: props.item.dl_issued_by_who,
  dl_issued_date: props.item.dl_issued_date ? moment(props.item.dl_issued_date).format('DD.MM.YYYY') : null,
  dl_expire_date: props.item.dl_expire_date ? moment(props.item.dl_expire_date).format('DD.MM.YYYY') : null,
  dl_files: props.item.dl_files || [],
  dl_upload_files: [],
  dl_ocr_upload_files: [],
  other_files: props.item.other_files || [],
  other_upload_files: [],
});
const rules = computed(() => {
  return {
    email: {
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

const tabValue = ref("0");

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

const onYandexPassportOCR = async (data) => {
  // console.log("Yandex OCR Passport", data);

  if (data.entities && data.entities.length > 0) {
    data.entities.forEach(entity => {
      switch (entity.name) {
        case "gender":
          state.gender = entity.text === "муж" ? 0 : 1;
          break;
        case "number":
          state.passport_series_number = entity.text.slice(0, 4) + " " + entity.text.slice(4);
          break;
        case "issued_by":
          state.passport_issued_by = entity.text;
          break;
        case "subdivision":
          state.passport_department_code = entity.text;
          break;
        case "issue_date":
          state.passport_date_of_issue = moment(entity.text, 'DD.MM.YYYY').format('DD.MM.YYYY');
          break;
        case "birth_date":
          state.birthday = moment(entity.text, 'DD.MM.YYYY').format('DD.MM.YYYY');
          break;
        case "birth_place":
          state.passport_born_place = entity.text;
          break;
        case "surname":
          state.lastName = entity.text.charAt(0).toUpperCase() + entity.text.slice(1);
          break;
        case "name":
          state.firstName = entity.text.charAt(0).toUpperCase() + entity.text.slice(1);
          break;
        case "middle_name":
          state.patronym = entity.text.charAt(0).toUpperCase() + entity.text.slice(1);
          break;
      }
    })

    state.passport_ocr_upload_files = data.files.target.files;
  }
}

const onYandexDriverLicenseOCR = async (data) => {
  // console.log("Yandex OCR DriverLicense", data);

  if (data.entities && data.entities.length > 0) {
    data.entities.forEach(entity => {
      switch (entity.name) {
        case "number":
          state.dl_series_number = entity.text;
          break;
        case "issue_date":
          state.dl_issued_date = moment(entity.text, 'DD.MM.YYYY').format('DD.MM.YYYY');
          break;
        case "expiration_date":
          state.dl_expire_date = moment(entity.text, 'DD.MM.YYYY').format('DD.MM.YYYY');
          break;
        case "birth_date":
          state.birthday = moment(entity.text, 'DD.MM.YYYY').format('DD.MM.YYYY');
          break;
        case "surname":
          state.lastName = entity.text.charAt(0).toUpperCase() + entity.text.slice(1);
          break;
        case "name":
          state.firstName = entity.text.charAt(0).toUpperCase() + entity.text.slice(1);
          break;
        case "middle_name":
          state.patronym = entity.text.charAt(0).toUpperCase() + entity.text.slice(1);
          break;
      }
    })

    state.dl_ocr_upload_files = data.files.target.files;
  }
}

const onOdysseyResult = async (data) => {
  odysseyResult.value = data;
  isOdysseyResultModalOpen.value = true;
  await fetchOdysseyResults();
}

const openOdysseyUrl = (url) => {
  window.open(url, '_blank');
}

async function fetchAdvertising() {
  advertising_type.value = (await DirectoryService.getAdvertisingTypes(user.company_id)).filter(advertising => advertising.archive === "Активен");
  loadingAdvertising.value = false;
}

async function fetchLegalPersons() {
  legalPersons.value = (await LegalPersonsService.getLegalPersons(user.company_id)).filter(person => person.status === "Активен");
  loadingLegalPersons.value = false;
}

async function fetchOdysseyResults() {
  odysseyResults.value = await OdysseyService.getResults(props.item.id);

  if(odysseyResults.value){
    odysseyResults.value.map(item => {
      item.created_at = new Date(item.created_at);
      item.birthday = new Date(item.birthday);
    });
  }

  loadingOdysseyResults.value = false;
}

const formatDate = (value, field) => {
  return moment(value[field]).format('DD.MM.YYYY');
};
const formatDateTime = (value, field) => {
  return moment(value[field]).format('DD.MM.YYYY HH:mm');
};

onMounted(() => {
  fetchAdvertising();
  fetchLegalPersons();
  fetchOdysseyResults();
});
</script>

<template>
  <div class="flex flex-wrap gap-4 mb-4">
    <YandexOCR
        v-if="state.archive === 0"
        @onPassportResult="onYandexPassportOCR"
        @onDriverLicenseResult="onYandexDriverLicenseOCR"
    />
    <Odyssey
        :id="props.item.id"
        :lastname="state.lastName"
        :firstname="state.firstName"
        :middlename="state.patronym"
        :birthday="state.birthday"
        :results="odysseyResults"
        :loading-results="loadingOdysseyResults"
        @onResult="onOdysseyResult"
    />
  </div>

  <Card class="xl:w-8/12 w-full">
    <template #title>Редактирование клиента
      <Badge v-if="state.archive === 1" value="Архив"></Badge>
    </template>
    <template #content>
      <Tabs :value="tabValue" scrollable>
        <TabList>
          <Tab value="0" class="flex gap-2">Основная информация</Tab>
          <Tab value="1" class="flex gap-2">Паспорт</Tab>
          <Tab value="2" class="flex gap-2">Водительское удостоверение</Tab>
          <Tab value="3" class="flex gap-2">Прочие документы</Tab>
          <Tab value="4" class="flex gap-2">Проверки в odyssey</Tab>
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
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
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
                  <label for="lastName"
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
                    <DatePickerWithMask :value="state.birthday" :key="state.birthday"
                                        @onChange="e => state.birthday = e"/>
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
                <!-- Номер банковской карты -->
                <div>
                  <label for="bankCard"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Номер банковской
                    карты</label>
                  <input
                      v-model="state.bank_card"
                      type="text" id="bankCard"
                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                      placeholder="..."
                  >
                </div>
                <Divider type="dashed"/>
                <!--  Вид рекламы -->
                <div>
                  <label for="position"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Вид рекламы</label>
                  <Select v-model="state.advertising" :loading="loadingAdvertising" :options="advertising_type"
                          optionLabel="name"
                          optionValue="id" placeholder="Выберите вид рекламы" showClear filter class="w-full">
                    <template v-if="user.access.directory === 2" #header>
                      <Button class="mt-4 ml-4" type="button" icon="pi pi-plus" label="Добавить" outlined
                              @click="isAdvertisingAddModalOpen = true"/>
                    </template>
                  </Select>
                </div>
                <!--  Организация клиента -->
                <div>
                  <label for="position"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Организация
                    клиента</label>
                  <Select v-model="state.legalPerson" :loading="loadingLegalPersons" :options="legalPersons"
                          optionLabel="full_name"
                          optionValue="id" placeholder="Выберите организацию клиента" showClear filter class="w-full">
                  </Select>
                </div>
                <!-- Примечание -->
                <div>
                  <label for="note"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Примечание</label>
                  <textarea
                      v-model="state.note"
                      type="text" id="note"
                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                      placeholder="..."
                  />
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
                <!-- Негатив -->
                <div class="flex items-center">
                  <input id="negative" type="checkbox"
                         v-model="state.negative"
                         class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"/>
                  <label for="negative" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    Негатив?
                  </label>
                </div>
                <!-- Должник -->
                <div class="flex items-center">
                  <input id="debtor" type="checkbox"
                         v-model="state.debtor"
                         class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"/>
                  <label for="debtor" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    Должник?
                  </label>
                </div>
                <!-- Отказ по проверке -->
                <div class="flex items-center">
                  <input id="verification_failure" type="checkbox"
                         v-model="state.verification_failure"
                         class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"/>
                  <label for="verification_failure" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    Отказ по проверке?
                  </label>
                </div>
                <Divider type="dashed"/>
                <!-- Активен? -->
                <div class="flex items-center">
                  <input id="active" type="checkbox"
                         v-model="state.active"
                         class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"/>
                  <label for="active" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    Активен?
                  </label>
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
                  <DatePickerWithMask :value="state.passport_date_of_issue" :key="state.passport_date_of_issue"
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
                <Divider type="dashed" v-if="state.passport_ocr_upload_files.length > 0"/>
                <!-- Паспорт. Распознанные файлы -->
                <FileGallery v-if="state.passport_ocr_upload_files.length > 0"
                             :ocr-items="state.passport_ocr_upload_files" :without-select="true"/>
                <Divider type="dashed"/>
                <!-- Паспорт. Файлы -->
                <FileGallery :items="state.passport_files" @onSelect="e => state.passport_upload_files = e.files"/>
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
                  <DatePickerWithMask :value="state.dl_issued_date" :key="state.dl_issued_date"
                                      @onChange="e => state.dl_issued_date = e"/>
                </div>
                <!-- Водительское удостоверение. Действуют до -->
                <div>
                  <label for="dl_expire_date"
                         class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Действуют до</label>
                  <DatePickerWithMask :value="state.dl_expire_date" :key="state.dl_expire_date"
                                      @onChange="e => state.dl_expire_date = e"/>
                </div>
                <Divider type="dashed" v-if="state.dl_ocr_upload_files.length > 0"/>
                <!-- Водительское удостоверение. Распознанные файлы -->
                <FileGallery v-if="state.dl_ocr_upload_files.length > 0" :ocr-items="state.dl_ocr_upload_files"
                             :without-select="true"/>
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
              <!-- Проверки Odyssey -->
              <Table
                  title="Проверки Odyssey"
                  :items="odysseyResults" :columns="odysseyColumns"
                  :loading="loadingOdysseyResults" :filters="odysseyFilters"
                  :filter-fields="odysseyFilterFields"
              >
                <template #columns>
                  <Column field="created_at" dataType="date" header="Дата" headerStyle="width: 10rem; min-width: 10rem;"
                          sortable>
                    <template #body="{ data }">
                      {{ formatDateTime(data, 'created_at') }}
                    </template>
                    <template #filter="{ filterModel }">
                      <DatePicker
                          v-model="filterModel.value"
                          dateFormat="dd.mm.yy" placeholder="дд.мм.гг"
                      />
                    </template>
                  </Column>
                  <Column field="scoring_overall_indicator" header="Cкоринг" dataType="numeric"
                          headerStyle="width: 10rem; min-width: 10rem;" sortable>
                    <template #filter="{ filterModel }">
                      <InputText v-model="filterModel.value" type="number" placeholder="Поиск по скорингу"/>
                    </template>
                  </Column>
                  <Column field="lastname" header="Фамилия" headerStyle="width: 10rem; min-width: 10rem;" sortable>
                    <template #filter="{ filterModel }">
                      <InputText v-model="filterModel.value" type="text" placeholder="Поиск по фамилии"/>
                    </template>
                  </Column>
                  <Column field="firstname" header="Имя" headerStyle="width: 10rem; min-width: 10rem;" sortable>
                    <template #filter="{ filterModel }">
                      <InputText v-model="filterModel.value" type="text" placeholder="Поиск по имени"/>
                    </template>
                  </Column>
                  <Column field="middlename" header="Отчество" headerStyle="width: 10rem; min-width: 10rem;" sortable>
                    <template #filter="{ filterModel }">
                      <InputText v-model="filterModel.value" type="text" placeholder="Поиск по отчеству"/>
                    </template>
                  </Column>
                  <Column field="birthday" dataType="date" header="Дата рождения"
                          headerStyle="width: 12rem; min-width: 12rem;" sortable>
                    <template #body="{ data }">
                      {{ formatDate(data, 'birthday') }}
                    </template>
                    <template #filter="{ filterModel }">
                      <DatePicker
                          v-model="filterModel.value"
                          dateFormat="dd.mm.yy" placeholder="дд.мм.гг"
                      />
                    </template>
                  </Column>
                  <Column header="Полный отчет" headerStyle="width: 8rem; min-width: 8rem;">
                    <template #body="slotProps">
                      <Button type="button" @click="openOdysseyUrl(slotProps.data.url)" icon="pi pi-search" severity="secondary" rounded></Button>
                    </template>
                  </Column>
                  <Column header="Инициатор" headerStyle="min-width: 30rem;">
                    <template #body="{ data }">
                      {{ data.init_full_name }} - {{data.init_email}}
                    </template>
                  </Column>
                </template>
              </Table>
            </TabPanel>
          </TabPanels>
          <Divider v-if="user.access.clients === 2" type="dashed"/>
          <p v-for="error of v$.$errors" :key="error.$uid" class="text-red-500">
            {{ error.$message }}
          </p>
          <div v-if="user.access.clients === 2 && state.archive === 0">
            <Button type="submit" icon="pi pi-save" label="Сохранить" :loading="sending" outlined/>
            <Button icon="pi pi-trash" label="В архив" class="ml-4" severity="secondary" :loading="sending"
                    @click="emit('onArchive');" outlined/>
            <Button v-if="user.id === 1" icon="pi pi-trash" label="Удалить" class="ml-4" severity="danger"
                    :loading="sending"
                    @click="emit('onDelete');" outlined/>
          </div>
          <div v-if="state.archive === 1">
            <Tag severity="warn" value="Клиент находится в архиве."/>
            <Button v-if="user.id === 1" icon="pi pi-trash" label="Удалить" class="ml-4" severity="danger"
                    :loading="sending"
                    @click="emit('onDelete');" outlined/>
          </div>
        </form>
      </Tabs>
    </template>
  </Card>
  <PopUpAddAdvertisingType :visible="isAdvertisingAddModalOpen" @on-add="onAdvertisingAdd"
                           @on-close="isAdvertisingAddModalOpen = false"/>
  <AlertModal :isOpen="isOdysseyResultModalOpen" @close="isOdysseyResultModalOpen = false"
              title="" accept
  >
    <template #body>
      <p>Значение общего показателя скоринга: <br/> <b class="text-3xl">{{ odysseyResult.scoring }}</b></p>
    </template>
    <template #buttons>
      <Button label="Смотреть полный отчет" @click="openOdysseyUrl(odysseyResult.url)"/>
      <Button label="Перейти к списку проверок" @click="() => {
        isOdysseyResultModalOpen = false;
        tabValue = '4';
      }"/>
    </template>
  </AlertModal>
</template>