<script setup>
import {computed, ref, watchEffect} from "vue";
import AlertModal from "@components/Modals/AlertModal.vue";
import ApiCloudService from "@services/ApiCloudService.js";
import {Icon} from "@vicons/utils";
import {DoneOutlined, CloseOutlined} from "@vicons/material";
import moment from "moment";

const props = defineProps({
  id: {
    type: String,
    required: true
  },
  state: {
    type: Object,
    required: true
  },
  viewResult: {
    type: Object,
    required: false,
    default: null
  },
  loadingResults: {
    type: Boolean,
    required: false,
    default: false
  }
});
const emit = defineEmits(['onResult', 'onRequestSend']);

const sending = ref(false);
const error = ref('');
const isAlertModalOpen = ref(false);
const isSelectModalOpen = ref(false);
const showRawResult = ref(true);

const gibdd_driver = computed(() => props.state.dl_series_number === null || props.state.dl_series_number === '' || props.state.dl_issued_date === null || props.state.dl_issued_date === '');
const rsa_kbm = computed(() => props.state.lastName === null || props.state.lastName === '' || props.state.firstName === null || props.state.firstName === '' || props.state.birthday === null || props.state.birthday === '' || props.state.dl_series_number === null || props.state.dl_series_number === '');
const fssp_physical = computed(() => props.state.lastName === null || props.state.lastName === '' || props.state.firstName === null || props.state.firstName === '' || props.state.birthday === null || props.state.birthday === '');
const nalog_inn = computed(() => props.state.passport_series_number === null || props.state.passport_series_number === '' || props.state.lastName === null || props.state.lastName === '' || props.state.firstName === null || props.state.firstName === '' || props.state.birthday === null || props.state.birthday === '');
const mvd_chekpassport = computed(() => props.state.passport_series_number === null || props.state.passport_series_number === '');
const bankrot_searchstring = computed(() => props.state.inn === null || props.state.inn === '');

const selectedVerification = ref('all');
const verifications = ref([
  {name: 'Все проверки разом', key: 'all'},
  {name: 'ГИБДД проверка ВУ', key: 'gibdd_driver'},
  {name: 'НСИС проверка КФ бонус/малус', key: 'rsa_kbm'},
  {name: 'ФССП поиск ФЛ', key: 'fssp_physical'},
  {name: 'ФНС поиск ИНН ФЛ', key: 'nalog_inn'},
  {name: 'МВД проверка паспорта РФ', key: 'mvd_chekpassport'},
  {name: 'Поиск в базе банкротств', key: 'bankrot_searchstring'},
]);
const gibdd_driver_verification = ref({
  done: false,
  error: false,
  result: null
});
const rsa_kbm_verification = ref({
  done: false,
  error: false,
  result: null
});
const fssp_physical_verification = ref({
  done: false,
  error: false,
  result: null
});
const nalog_inn_verification = ref({
  done: false,
  error: false,
  result: null
});
const mvd_chekpassport_verification = ref({
  done: false,
  error: false,
  result: null
});
const bankrot_searchstring_verification = ref({
  done: false,
  error: false,
  result: null
});

const result = ref(null);
const regions = {
  "-1": "Все регионы",
  "01": "Республика Адыгея",
  "02": "Республика Башкортостан",
  "03": "Республика Бурятия",
  "04": "Республика Алтай",
  "05": "Республика Дагестан",
  "06": "Республика Ингушетия",
  "07": "Кабардино-Балкария",
  "08": "Республика Калмыкия",
  "09": "Карачаево-Черкесия",
  "10": "Республика Карелия",
  "11": "Республика Коми",
  "12": "Республика Марий Эл",
  "13": "Республика Мордовия",
  "14": "Республика Саха (Якутия)",
  "15": "Северная Осетия-Алания",
  "16": "Республика Татарстан",
  "17": "Республика Тыва",
  "18": "Удмуртская Республика",
  "19": "Республика Хакасия",
  "20": "Чеченская Республика",
  "21": "Чувашская Республика - Чувашия",
  "22": "Алтайский край",
  "23": "Краснодарский край",
  "24": "Красноярский край",
  "25": "Приморский край",
  "26": "Ставропольский край",
  "27": "Хабаровский край и Еврейская автономная область",
  "28": "Амурская область",
  "29": "Архангельская область и Ненецкий автономный округ",
  "30": "Астраханская область",
  "31": "Белгородская область",
  "32": "Брянская область",
  "33": "Владимирская область",
  "34": "Волгоградская область",
  "35": "Вологодская область",
  "36": "Воронежская область",
  "37": "Ивановская область",
  "38": "Иркутская область",
  "39": "Калининградская область",
  "40": "Калужская область",
  "41": "Камчатский край и Чукотский автономный округ",
  "42": "Кемеровская область - Кузбасс",
  "43": "Кировская область",
  "44": "Костромская область",
  "45": "Курганская область",
  "46": "Курская область",
  "47": "Ленинградская область",
  "48": "Липецкая область",
  "49": "Магаданская область",
  "50": "Московская область",
  "51": "Мурманская область",
  "52": "Нижегородская область",
  "53": "Новгородская область",
  "54": "Новосибирская область",
  "55": "Омская область",
  "56": "Оренбургская область",
  "57": "Орловская область",
  "58": "Пензенская область",
  "59": "Пермский край",
  "60": "Псковская область",
  "61": "Ростовская область",
  "62": "Рязанская область",
  "63": "Самарская область",
  "64": "Саратовская область",
  "65": "Сахалинская область",
  "66": "Свердловская область",
  "67": "Смоленская область",
  "68": "Тамбовская область",
  "69": "Тверская область",
  "70": "Томская область",
  "71": "Тульская область",
  "72": "Тюменская область",
  "73": "Ульяновская область",
  "74": "Челябинская область",
  "75": "Забайкальский край",
  "76": "Ярославская область",
  "77": "Москва",
  "78": "Санкт-Петербург",
  "86": "Ханты-Мансийский АО",
  "89": "Ямало-Ненецкий АО",
  "82": "Республика Крым",
  "92": "Севастополь"
}
const kbmDefaultValue = 1.17;

watchEffect(() => {
  if (props.viewResult !== null) {
    selectedVerification.value = props.viewResult.type;

    switch (props.viewResult.type) {
      case 'gibdd_driver':
        gibdd_driver_verification.value = {
          done: true,
          error: false,
          result: props.viewResult
        }
        break;

      case 'rsa_kbm':
        rsa_kbm_verification.value = {
          done: true,
          error: false,
          result: props.viewResult
        }
        break;

      case 'fssp_physical':
        fssp_physical_verification.value = {
          done: true,
          error: false,
          result: props.viewResult
        }
        break;

      case 'nalog_inn':
        nalog_inn_verification.value = {
          done: true,
          error: false,
          result: props.viewResult
        }
        break;

      case 'mvd_chekpassport':
        mvd_chekpassport_verification.value = {
          done: true,
          error: false,
          result: props.viewResult
        }
        break;

      case 'bankrot_searchstring':
        bankrot_searchstring_verification.value = {
          done: true,
          error: false,
          result: props.viewResult
        }
        break;
    }

    isSelectModalOpen.value = true;
    result.value = true;
  }
})

async function prepareRequest() {
  sending.value = true;

  const passport_serial = props.state.passport_series_number ? props.state.passport_series_number.slice(0, 4).trim() : "";
  const passport_number = props.state.passport_series_number ? props.state.passport_series_number.slice(4).trim() : "";
  const driver_serial = props.state.dl_series_number ? props.state.dl_series_number.slice(0, 4).trim() : "";
  const driver_number = props.state.dl_series_number ? props.state.dl_series_number.slice(4).trim() : "";
  const driver_issue_date = props.state.dl_issued_date;

  setTimeout(async () => {
    switch (selectedVerification.value) {
      case 'all':
        await sendRequest('gibdd_driver', {
          serianomer: driver_serial + driver_number,
          date: driver_issue_date,
          user_id: props.id
        }, gibdd_driver_verification, gibdd_driver);
        await sendRequest('rsa_kbm', {
          surname: props.state.lastName,
          name: props.state.firstName,
          secondname: props.state.patronym,
          birthday: props.state.birthday,
          seria: driver_serial,
          nomer: driver_number,
          user_id: props.id
        }, rsa_kbm_verification, rsa_kbm);
        await sendRequest('fssp_physical', {
          surname: props.state.lastName,
          name: props.state.firstName,
          secondname: props.state.patronym,
          birthday: props.state.birthday,
          user_id: props.id
        }, fssp_physical_verification, fssp_physical);
        await sendRequest('nalog_inn', {
          surname: props.state.lastName,
          name: props.state.firstName,
          birthday: props.state.birthday,
          serianomer: passport_serial + passport_number,
          user_id: props.id
        }, nalog_inn_verification, nalog_inn);
        await sendRequest('mvd_chekpassport', {
          seria: passport_serial,
          nomer: passport_number,
          user_id: props.id
        }, mvd_chekpassport_verification, mvd_chekpassport);
        await sendRequest('bankrot_searchstring', {
          inn: props.state.inn,
          user_id: props.id
        }, bankrot_searchstring_verification, bankrot_searchstring);
        setTimeout(() => {
          result.value = true;
          sending.value = false;
        }, 500);
        break;

      case 'gibdd_driver':
        await sendRequest('gibdd_driver', {
          serianomer: driver_serial + driver_number,
          date: driver_issue_date,
          user_id: props.id
        }, gibdd_driver_verification, gibdd_driver);
        setTimeout(() => {
          result.value = true;
          sending.value = false;
        }, 500);
        break;

      case 'rsa_kbm':
        await sendRequest('rsa_kbm', {
          surname: props.state.lastName,
          name: props.state.firstName,
          secondname: props.state.patronym,
          birthday: props.state.birthday,
          seria: driver_serial,
          nomer: driver_number,
          user_id: props.id
        }, rsa_kbm_verification, rsa_kbm);
        setTimeout(() => {
          result.value = true;
          sending.value = false;
        }, 500);
        break;

      case 'fssp_physical':
        await sendRequest('fssp_physical', {
          surname: props.state.lastName,
          name: props.state.firstName,
          secondname: props.state.patronym,
          birthday: props.state.birthday,
          user_id: props.id
        }, fssp_physical_verification, fssp_physical);
        setTimeout(() => {
          result.value = true;
          sending.value = false;
        }, 500);
        break;

      case 'nalog_inn':
        await sendRequest('nalog_inn', {
          surname: props.state.lastName,
          name: props.state.firstName,
          birthday: props.state.birthday,
          serianomer: passport_serial + passport_number,
          user_id: props.id
        }, nalog_inn_verification, nalog_inn);
        setTimeout(() => {
          result.value = true;
          sending.value = false;
        }, 500);
        break;

      case 'mvd_chekpassport':
        await sendRequest('mvd_chekpassport', {
          seria: passport_serial,
          nomer: passport_number,
          user_id: props.id
        }, mvd_chekpassport_verification, mvd_chekpassport);
        setTimeout(() => {
          result.value = true;
          sending.value = false;
        }, 500);
        break;

      case 'bankrot_searchstring':
        await sendRequest('bankrot_searchstring', {
          inn: props.state.inn,
          user_id: props.id
        }, bankrot_searchstring_verification, bankrot_searchstring);
        setTimeout(() => {
          result.value = true;
          sending.value = false;
        }, 500);
        break;
    }
  }, 1000);
}

async function sendRequest(type, data, verification, rule) {
  let result = null;

  if (!rule.value)
    result = await ApiCloudService.sendRequest(type, data);

  verification.value.done = true;
  verification.value.result = result;

  if (result === null) {
    verification.value.error = rule.value ? 'Запрос не может быть отправлен, есть невыполненные условия' : 'Ошибка при отправке запроса.';
  } else if (result.error) {
    verification.value.error = result.message;
  } else if (result.status === 404) {
    verification.value.error = result.message;
  }

  emit('onRequestSend', {
    type: type,
    data: data,
    rule: rule.value,
    result: result,
    error: verification.value.error
  })
}

const openDrawer = () => {
  isSelectModalOpen.value = true;
}

const sendNalogINNRequest = async () => {
  selectedVerification.value = 'nalog_inn';

  await prepareRequest();
}

defineExpose({
  openDrawer,
  sendNalogINNRequest
})
</script>

<template>
  <div>
    <Button icon="pi pi-upload" label="Проверить в api-cloud" :loading="sending || loadingResults" outlined
            @click="isSelectModalOpen = true"/>
  </div>

  <AlertModal :isOpen="isAlertModalOpen" @close="isAlertModalOpen = false" :title="error" info/>
  <Drawer
      position="right" class="w-full md:w-3/5 lg:w-2/5 xl:w-2/6"
      v-bind:visible="isSelectModalOpen"
      v-on:update:visible="() => {
        if(!sending) {
          if(result)
            emit('onResult');

          result = null;
          sending = false;
          isSelectModalOpen = false;
          selectedVerification = 'all';
          gibdd_driver_verification = {done: false, result: null, error: false};
          rsa_kbm_verification = {done: false, result: null, error: false};
          fssp_physical_verification = {done: false, result: null, error: false};
          nalog_inn_verification = {done: false, result: null, error: false};
          mvd_chekpassport_verification = {done: false, result: null, error: false};
          bankrot_searchstring_verification = {done: false, result: null, error: false};
        }
      }"
      :header="!result ? (!sending ? 'Выберите проверку:' : 'Проводится проверка') : 'Результат:'"
      :show-close-icon="!sending"
  >
    <div class="flex">
      <div v-if="!result && !sending" class="flex flex-col gap-4">
        <div class="flex items-center gap-2">
          <RadioButton v-model="selectedVerification" inputId="all" name="verification" value="all"/>
          <label for="all" class="flex flex-col">Все проверки разом</label>
        </div>
        <!-- gibdd -->
        <div class="flex items-center gap-2">
          <RadioButton
              v-model="selectedVerification" inputId="gibdd_driver" name="verification" value="gibdd_driver"
              :disabled="gibdd_driver"
          />
          <label for="gibdd_driver" class="flex flex-col">
            ГИБДД проверка ВУ
            <span v-if="state.dl_series_number === null || state.dl_series_number === ''" class="text-red-600">Не заполнено: Серия и Номер ВУ</span>
            <span v-if="state.dl_issued_date === null || state.dl_issued_date === ''" class="text-red-600">Не заполнено: Дата выдачи ВУ</span>
          </label>
        </div>
        <!-- kbm -->
        <div class="flex items-center gap-2">
          <RadioButton
              v-model="selectedVerification" inputId="rsa_kbm" name="verification" value="rsa_kbm"
              :disabled="rsa_kbm"
          />
          <label for="rsa_kbm" class="flex flex-col">
            НСИС проверка КФ бонус/малус
            <span v-if="state.lastName === null || state.lastName === ''"
                  class="text-red-600">Не заполнено: Фамилия</span>
            <span v-if="state.firstName === null || state.firstName === ''"
                  class="text-red-600">Не заполнено: Имя</span>
            <span v-if="state.birthday === null || state.birthday === ''" class="text-red-600">Не заполнено: Дата рождения</span>
            <span v-if="state.dl_series_number === null || state.dl_series_number === ''" class="text-red-600">Не заполнено: Серия и Номер ВУ</span>
          </label>
        </div>
        <!-- physical -->
        <div class="flex items-center gap-2">
          <RadioButton
              v-model="selectedVerification" inputId="fssp_physical" name="verification" value="fssp_physical"
              :disabled="fssp_physical"
          />
          <label for="fssp_physical" class="flex flex-col">
            ФССП поиск ФЛ
            <span v-if="state.lastName === null || state.lastName === ''"
                  class="text-red-600">Не заполнено: Фамилия</span>
            <span v-if="state.firstName === null || state.firstName === ''"
                  class="text-red-600">Не заполнено: Имя</span>
            <span v-if="state.birthday === null || state.birthday === ''" class="text-red-600">Не заполнено: Дата рождения</span>
          </label>
        </div>
        <!-- nalog -->
        <div class="flex items-center gap-2 hidden">
          <RadioButton
              v-model="selectedVerification" inputId="nalog_inn" name="verification" value="nalog_inn"
              :disabled="nalog_inn"
          />
          <label for="nalog_inn" class="flex flex-col">
            ФНС поиск ИНН ФЛ
            <span v-if="state.passport_series_number === null || state.passport_series_number === ''"
                  class="text-red-600">Не заполнено: Серия и номер паспорта</span>
            <span v-if="state.lastName === null || state.lastName === ''"
                  class="text-red-600">Не заполнено: Фамилия</span>
            <span v-if="state.firstName === null || state.firstName === ''"
                  class="text-red-600">Не заполнено: Имя</span>
            <span v-if="state.birthday === null || state.birthday === ''" class="text-red-600">Не заполнено: Дата рождения</span>
          </label>
        </div>
        <!-- passport -->
        <div class="flex items-center gap-2">
          <RadioButton
              v-model="selectedVerification" inputId="mvd_chekpassport" name="verification" value="mvd_chekpassport"
              :disabled="mvd_chekpassport"
          />
          <label for="mvd_chekpassport" class="flex flex-col">
            МВД проверка паспорта РФ
            <span v-if="state.passport_series_number === null || state.passport_series_number === ''"
                  class="text-red-600">Не заполнено: Серия и номер паспорта</span>
          </label>
        </div>
        <!-- bankrot -->
        <div class="flex items-center gap-2">
          <RadioButton
              v-model="selectedVerification" inputId="bankrot_searchstring" name="verification"
              value="bankrot_searchstring"
              :disabled="bankrot_searchstring"
          />
          <label for="bankrot_searchstring" class="flex flex-col">
            Поиск в базе банкротств
            <span v-if="state.inn === null || state.inn === ''" class="text-red-600">Не заполнено: ИНН</span>
          </label>
        </div>
      </div>
      <div v-else-if="sending" class="flex flex-col gap-4 w-full">
        <!-- gibdd -->
        <div v-if="selectedVerification === 'all' || selectedVerification === 'gibdd_driver'"
             class="flex justify-between items-center">
          <div>
            ГИБДД проверка ВУ
          </div>
          <div v-if="!gibdd_driver_verification.done">
            <ProgressSpinner class="flex justify-end" style="width: 32px; height: 32px" strokeWidth="4"/>
          </div>
          <div v-else-if="gibdd_driver_verification.error">
            <Icon size="32" color="red">
              <CloseOutlined/>
            </Icon>
          </div>
          <div v-else>
            <Icon size="32" color="green">
              <DoneOutlined/>
            </Icon>
          </div>
        </div>
        <!-- kbm -->
        <div v-if="selectedVerification === 'all' || selectedVerification === 'rsa_kbm'"
             class="flex justify-between items-center">
          <div>
            НСИС проверка КФ бонус/малус
          </div>
          <div v-if="!rsa_kbm_verification.done">
            <ProgressSpinner class="flex justify-end" style="width: 32px; height: 32px" strokeWidth="4"/>
          </div>
          <div v-else-if="rsa_kbm_verification.error">
            <Icon size="32" color="red">
              <CloseOutlined/>
            </Icon>
          </div>
          <div v-else>
            <Icon size="32" color="green">
              <DoneOutlined/>
            </Icon>
          </div>
        </div>
        <!-- physical -->
        <div v-if="selectedVerification === 'all' || selectedVerification === 'fssp_physical'"
             class="flex justify-between items-center">
          <div>
            ФССП поиск ФЛ
          </div>
          <div v-if="!fssp_physical_verification.done">
            <ProgressSpinner class="flex justify-end" style="width: 32px; height: 32px" strokeWidth="4"/>
          </div>
          <div v-else-if="fssp_physical_verification.error">
            <Icon size="32" color="red">
              <CloseOutlined/>
            </Icon>
          </div>
          <div v-else>
            <Icon size="32" color="green">
              <DoneOutlined/>
            </Icon>
          </div>
        </div>
        <!-- nalog -->
        <div v-if="selectedVerification === 'all' || selectedVerification === 'nalog_inn'"
             class="flex justify-between items-center">
          <div>
            ФНС поиск ИНН ФЛ
          </div>
          <div v-if="!nalog_inn_verification.done">
            <ProgressSpinner class="flex justify-end" style="width: 32px; height: 32px" strokeWidth="4"/>
          </div>
          <div v-else-if="nalog_inn_verification.error">
            <Icon size="32" color="red">
              <CloseOutlined/>
            </Icon>
          </div>
          <div v-else>
            <Icon size="32" color="green">
              <DoneOutlined/>
            </Icon>
          </div>
        </div>
        <!-- passport -->
        <div v-if="selectedVerification === 'all' || selectedVerification === 'mvd_chekpassport'"
             class="flex justify-between items-center">
          <div>
            МВД проверка паспорта РФ
          </div>
          <div v-if="!mvd_chekpassport_verification.done">
            <ProgressSpinner class="flex justify-end" style="width: 32px; height: 32px" strokeWidth="4"/>
          </div>
          <div v-else-if="mvd_chekpassport_verification.error">
            <Icon size="32" color="red">
              <CloseOutlined/>
            </Icon>
          </div>
          <div v-else>
            <Icon size="32" color="green">
              <DoneOutlined/>
            </Icon>
          </div>
        </div>
        <!-- bankrot -->
        <div v-if="selectedVerification === 'all' || selectedVerification === 'bankrot_searchstring'"
             class="flex justify-between items-center">
          <div>
            Поиск в базе банкротств
          </div>
          <div v-if="!bankrot_searchstring_verification.done">
            <ProgressSpinner class="flex justify-end" style="width: 32px; height: 32px" strokeWidth="4"/>
          </div>
          <div v-else-if="bankrot_searchstring_verification.error">
            <Icon size="32" color="red">
              <CloseOutlined/>
            </Icon>
          </div>
          <div v-else>
            <Icon size="32" color="green">
              <DoneOutlined/>
            </Icon>
          </div>
        </div>
      </div>
      <div v-else class="flex flex-col gap-4 w-full">
        <!-- gibdd -->
        <div v-if="selectedVerification === 'all' || selectedVerification === 'gibdd_driver'">
          <Panel header="ГИБДД проверка ВУ" toggleable>
            <div v-if="gibdd_driver_verification.error" class="text-red-600">
              {{ gibdd_driver_verification.error }}
            </div>
            <div v-else class="flex flex-col gap-2">
              <span class="text-blue-400">{{ gibdd_driver_verification.result.request }}</span>
              <div>
                <span v-if="gibdd_driver_verification.result.message">{{
                    gibdd_driver_verification.result.message
                  }}</span>
                <div v-for="(value, key) in gibdd_driver_verification.result.doc" :key="key">
                  <span>{{ key }}: </span><span class="font-bold">{{ value }}</span>
                </div>
              </div>
            </div>
            <Panel v-if="showRawResult" header="Необработанный ответ" class="mt-4" toggleable collapsed>
              {{ gibdd_driver_verification.result }}
            </Panel>
          </Panel>
        </div>
        <!-- kbm -->
        <div v-if="selectedVerification === 'all' || selectedVerification === 'rsa_kbm'">
          <Panel header="НСИС проверка КФ бонус/малус" toggleable>
            <div v-if="rsa_kbm_verification.error">
              {{ rsa_kbm_verification.error }}
              <span v-if="rsa_kbm_verification.result?.errormsg">{{ rsa_kbm_verification.result?.errormsg }}</span>
              <span v-if="rsa_kbm_verification.result?.details">{{ rsa_kbm_verification.result?.details }}</span>
            </div>
            <div v-else class="flex flex-col gap-2">
              <span class="text-blue-400">{{ rsa_kbm_verification.result.request }}</span>
              <div>
                <span>Коэффициент КБМ: </span>
                <span
                    class="font-bold"
                    :class="{
                      'text-green-600': parseFloat(rsa_kbm_verification.result.kbmValue) < kbmDefaultValue,
                      'text-red-600': parseFloat(rsa_kbm_verification.result.kbmValue) > kbmDefaultValue,
                    }"
                >
                  {{ rsa_kbm_verification.result.kbmValue }}
                </span>
              </div>
              <div>
                <span v-if="parseFloat(rsa_kbm_verification.result.kbmValue) === kbmDefaultValue">
                  Новый водитель или информация не найдена
                </span>
                <span v-else-if="parseFloat(rsa_kbm_verification.result.kbmValue) > kbmDefaultValue" class="text-red-600">
                  Аварийный водитель
                </span>
              </div>
            </div>
            <Panel v-if="showRawResult" header="Необработанный ответ" class="mt-4" toggleable collapsed>
              {{ rsa_kbm_verification.result }}
            </Panel>
          </Panel>
        </div>
        <!-- physical -->
        <div v-if="selectedVerification === 'all' || selectedVerification === 'fssp_physical'">
          <Panel header="ФССП поиск ФЛ" toggleable>
            <div v-if="fssp_physical_verification.error">
              {{ fssp_physical_verification.error }}
            </div>
            <div v-else class="flex flex-col gap-2">
              <span class="text-blue-400">{{ fssp_physical_verification.result.request }}</span>
              <div>
                <span v-if="fssp_physical_verification.result.message">{{
                    fssp_physical_verification.result.message
                  }}</span>
                <span v-if="fssp_physical_verification.result.info">{{ fssp_physical_verification.result.info }}</span>
                <Panel
                    v-if="fssp_physical_verification.result.records && fssp_physical_verification.result.records.length > 0"
                    v-for="record in fssp_physical_verification.result.records.sort((a, b) => moment(b.process_date, 'DD.MM.YYYY').diff(moment(a.process_date, 'DD.MM.YYYY')))"
                    class="mt-4" toggleable collapsed
                >
                  <template #header>
                    {{ record.document_type }} от {{ record.process_date }}
                  </template>

                  <div class="flex flex-col gap-2">
                    <div class="flex justify-between">
                      <span class="text-2xl">Общая сумма:</span><span
                        class="text-amber-700 text-2xl font-bold">{{ record.sum }}</span>
                    </div>

                    <div v-for="subject in record.subjectArray" class="flex justify-between">
                      <span class="text-wrap w-60 max-w-90">{{ subject.title }}:</span><span
                        class="text-amber-700 font-bold">{{ subject.sum }}</span>
                    </div>

                    <Divider type="dashed"/>

                    <div class="flex flex-col">
                      <span>Тип дела:</span><span class="font-bold">{{ record.subject }}</span>
                    </div>
                    <div class="flex flex-col">
                      <span>Номер дела:</span><span class="font-bold">{{ record.process_title }}</span>
                    </div>
                    <div class="flex flex-col">
                      <span>Дата дела:</span><span class="font-bold">{{ record.process_date }}</span>
                    </div>
                    <div class="flex flex-col">
                      <span>Исполнительный документ:</span><span class="font-bold">{{ record.recIspDoc }}</span>
                    </div>
                    <div v-if="record.stopIP" class="flex flex-col">
                      <span>stopIP:</span><span class="font-bold">{{ record.stopIP }}</span>
                    </div>
                    <div class="flex flex-col">
                      <span>Организация инициатор:</span><span
                        class="font-bold">{{ record.document_organization }}</span>
                    </div>
                    <div class="flex flex-col">
                      <span>Информация о приставе:</span><span class="font-bold">{{ record.officer_name }}</span>
                    </div>
                    <div class="flex flex-col">
                      <span>Контакты пристава:</span><span
                        class="font-bold">{{ record.officer_phones.join(',') }}</span>
                    </div>
                  </div>

                </Panel>
              </div>
            </div>
            <Panel v-if="showRawResult" header="Необработанный ответ" class="mt-4" toggleable collapsed>
              {{ fssp_physical_verification.result }}
            </Panel>
          </Panel>
        </div>
        <!-- nalog -->
        <div v-if="selectedVerification === 'all' || selectedVerification === 'nalog_inn'">
          <Panel header="ФНС поиск ИНН ФЛ" toggleable>
            <div v-if="nalog_inn_verification.error">
              {{ nalog_inn_verification.error }}
              <div>
                <span v-if="state.passport_series_number === null || state.passport_series_number === ''"
                      class="text-red-600">Не заполнено: Серия и номер паспорта</span>
                <span v-if="state.lastName === null || state.lastName === ''"
                      class="text-red-600">Не заполнено: Фамилия</span>
                <span v-if="state.firstName === null || state.firstName === ''"
                      class="text-red-600">Не заполнено: Имя</span>
                <span v-if="state.birthday === null || state.birthday === ''" class="text-red-600">Не заполнено: Дата рождения</span>
              </div>
            </div>
            <div v-else class="flex flex-col gap-2">
              <span class="text-blue-400">{{ nalog_inn_verification.result.request }}</span>
              <div>
                <span v-if="nalog_inn_verification.result.message">{{ nalog_inn_verification.result.message }}</span>
                <span v-if="nalog_inn_verification.result.info">{{ nalog_inn_verification.result.info }}</span>
              </div>
            </div>
            <Panel v-if="showRawResult && nalog_inn_verification.result" header="Необработанный ответ" class="mt-4" toggleable collapsed>
              {{ nalog_inn_verification.result }}
            </Panel>
          </Panel>
        </div>
        <!-- passport -->
        <div v-if="selectedVerification === 'all' || selectedVerification === 'mvd_chekpassport'">
          <Panel header="МВД проверка паспорта РФ" toggleable>
            <div v-if="mvd_chekpassport_verification.error" class="text-red-600">
              {{ mvd_chekpassport_verification.error }}
            </div>
            <div v-else class="flex flex-col gap-2">
              <span class="text-blue-400">{{ mvd_chekpassport_verification.result.request }}</span>
              <div>
                <span v-if="mvd_chekpassport_verification.result.message">{{
                    mvd_chekpassport_verification.result.message
                  }}</span>
                <span v-if="mvd_chekpassport_verification.result.info">{{
                    mvd_chekpassport_verification.result.info
                  }}</span>
              </div>
            </div>
            <Panel v-if="showRawResult" header="Необработанный ответ" class="mt-4" toggleable collapsed>
              {{ mvd_chekpassport_verification.result }}
            </Panel>
          </Panel>
        </div>
        <!-- bankrot -->
        <div v-if="selectedVerification === 'all' || selectedVerification === 'bankrot_searchstring'">
          <Panel header="Поиск в базе банкротств" toggleable>
            <div v-if="bankrot_searchstring_verification.error" class="text-red-600">
              {{ bankrot_searchstring_verification.error }}
            </div>
            <div v-else class="flex flex-col gap-2">
              <span class="text-blue-400">{{ bankrot_searchstring_verification.result.request }}</span>
              <div>
                <span v-if="bankrot_searchstring_verification.result.message">{{
                    bankrot_searchstring_verification.result.message
                  }}</span>
                <span v-if="bankrot_searchstring_verification.result.info">{{
                    bankrot_searchstring_verification.result.info
                  }}</span>
              </div>
            </div>
            <Panel v-if="showRawResult" header="Необработанный ответ" class="mt-4" toggleable collapsed>
              {{ bankrot_searchstring_verification.result }}
            </Panel>
          </Panel>
        </div>
      </div>
    </div>
    <div class="mt-8">
      <Button v-if="!result && !sending" label="Проверить" :loading="sending" @click="prepareRequest"/>
    </div>
  </Drawer>
</template>