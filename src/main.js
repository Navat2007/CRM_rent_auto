import {createApp} from 'vue'
import {createPinia} from "pinia";
import router from '@router'
import PrimeVue from 'primevue/config';
import Aura from '@primevue/themes/aura';
import {definePreset} from "@primevue/themes";

import App from '@/App.vue'
import '@/style.css'
import 'primeicons/primeicons.css'

const app = createApp(App);

const MyPreset = definePreset(Aura, {
    semantic: {
        colorScheme: {
            light: {
                primary: {
                    "50": "#FFF7ED",
                    "100": "#FFEDD5",
                    "200": "#FED7AA",
                    "300": "#FDBA74",
                    "400": "#FB923C",
                    "500": "#F97316",
                    "600": "#EA580C",
                    "700": "#C2410C",
                    "800": "#9A3412",
                    "900": "#7C2D12",
                    "950": "#431407"
                },
                secondary: {
                    "50": "#EEF2FF",
                    "100": "#E0E7FF",
                    "200": "#C7D2FE",
                    "300": "#A5B4FC",
                    "400": "#818CF8",
                    "500": "#6366F1",
                    "600": "#4F46E5",
                    "700": "#4338CA",
                    "800": "#3730A3",
                    "900": "#312E81",
                    "950": "#1E1B4B"
                },
            },
            dark: {
                primary: {
                    "50": "#eff6ff",
                    "100": "#dbeafe",
                    "200": "#bfdbfe",
                    "300": "#93c5fd",
                    "400": "#60a5fa",
                    "500": "#3b82f6",
                    "600": "#2563eb",
                    "700": "#1d4ed8",
                    "800": "#1e40af",
                    "900": "#1e3a8a",
                    "950": "#172554"
                },
                secondary: {
                    "50": "#FFF7ED",
                    "100": "#FFEDD5",
                    "200": "#FED7AA",
                    "300": "#FDBA74",
                    "400": "#FB923C",
                    "500": "#F97316",
                    "600": "#EA580C",
                    "700": "#C2410C",
                    "800": "#9A3412",
                    "900": "#7C2D12",
                    "950": "#431407"
                }
            }
        }
    }
});

app.use(createPinia());
app.use(router);
app.use(PrimeVue, {
    ripple: true,
    locale: {
        "accept": "Да",
        "addRule": "Добавить правило",
        "am": "до полудня",
        "apply": "Принять",
        "cancel": "Отмена",
        "choose": "Выбрать",
        "chooseDate": "Выбрать дату",
        "chooseMonth": "Выбрать месяц",
        "chooseYear": "Выбрать год",
        "clear": "Очистить",
        "completed": "Завершено",
        "contains": "Содержит",
        "custom": "Пользовательский",
        "dateAfter": "Дата после",
        "dateBefore": "Дата до",
        "dateFormat": "dd.mm.yy",
        "dateIs": "Дата равна",
        "dateIsNot": "Дата не равна",
        "dayNames": [
            "Воскресенье",
            "Понедельник",
            "Вторник",
            "Среда",
            "Четверг",
            "Пятница",
            "Суббота"
        ],
        "dayNamesMin": [
            "Вс",
            "Пн",
            "Вт",
            "Ср",
            "Чт",
            "Пт",
            "Сб"
        ],
        "dayNamesShort": [
            "Вск",
            "Пнд",
            "Втр",
            "Срд",
            "Чтв",
            "Птн",
            "Сбт"
        ],
        "emptyFilterMessage": "Результатов не найдено",
        "emptyMessage": "Нет доступных вариантов",
        "emptySearchMessage": "Результатов не найдено",
        "emptySelectionMessage": "Нет выбранного элемента",
        "endsWith": "Заканчивается",
        "equals": "Равно",
        "fileSizeTypes": [
            "Б",
            "Кб",
            "Мб",
            "Гб",
            "Тб",
            "Пб",
            "Эб",
            "Зб",
            "Йб"
        ],
        "filter": "Фильтр",
        "firstDayOfWeek": 1,
        "gt": "Более чем",
        "gte": "Более чем или равно",
        "lt": "Меньше чем",
        "lte": "Меньше чем или равно",
        "matchAll": "Сопоставить все",
        "matchAny": "Совпадение с любым",
        "medium": "Нормальный",
        "monthNames": [
            "Январь",
            "Февраль",
            "Март",
            "Апрель",
            "Май",
            "Июнь",
            "Июль",
            "Август",
            "Сентябрь",
            "Октябрь",
            "Ноябрь",
            "Декабрь"
        ],
        "monthNamesShort": [
            "Янв",
            "Фев",
            "Мар",
            "Апр",
            "Май",
            "Июн",
            "Июл",
            "Авг",
            "Сен",
            "Окт",
            "Ноя",
            "Дек"
        ],
        "nextDecade": "Следующее десятилетие",
        "nextHour": "Следующий час",
        "nextMinute": "Следующая минута",
        "nextMonth": "Следующий месяц",
        "nextSecond": "Следующая секунда",
        "nextYear": "Следующий год",
        "noFilter": "Нет фильтра",
        "notContains": "Не содержит",
        "notEquals": "Не равно",
        "now": "Сейчас",
        "passwordPrompt": "Введите пароль",
        "pending": "В ожидании",
        "pm": "после полудня",
        "prevDecade": "Предыдущее десятилетие",
        "prevHour": "Предыдущий час",
        "prevMinute": "Предыдущая минута",
        "prevMonth": "Предыдущий месяц",
        "prevSecond": "Предыдущая секунда",
        "prevYear": "Предыдущий год",
        "reject": "Нет",
        "removeRule": "Удалить правило",
        "searchMessage": "{0} результатов доступно",
        "selectionMessage": "{0} элементов выбрано",
        "showMonthAfterYear": false,
        "startsWith": "Начинается с",
        "strong": "Хороший",
        "today": "Сегодня",
        "upload": "Загрузить",
        "weak": "Простой",
        "weekHeader": "Нед.",
        "aria": {
            "cancelEdit": "Отменить правку",
            "close": "Закрыть",
            "collapseLabel": "Свернуть",
            "collapseRow": "Свернуть строку",
            "editRow": "Редактировать строку",
            "expandLabel": "Развернуть",
            "expandRow": "Развернуть строку",
            "falseLabel": "Неверно",
            "filterConstraint": "Ограничение фильтра",
            "filterOperator": "Оператор фильтра",
            "firstPageLabel": "Первая страница",
            "gridView": "В виде сетки",
            "hideFilterMenu": "Скрыть меню фильтра",
            "jumpToPageDropdownLabel": "Перейти к раскрывающемуся списку страниц",
            "jumpToPageInputLabel": "Перейти к вводу страницы",
            "lastPageLabel": "Последняя страница",
            "listView": "В виде списка",
            "moveAllToSource": "Переместить всё в источник",
            "moveAllToTarget": "Переместить всё в приёмник",
            "moveBottom": "Переместить в конец",
            "moveDown": "Переместить вниз",
            "moveTop": "Переместить в начало",
            "moveToSource": "Переместить в источник",
            "moveToTarget": "Переместить в приёмник",
            "moveUp": "Переместить вверх",
            "navigation": "Навигация",
            "next": "Следующий",
            "nextPageLabel": "Следующая страница",
            "nullLabel": "Не выбран",
            "otpLabel": "Введите символ одноразового пароля {0}",
            "pageLabel": "{page}",
            "passwordHide": "Скрыть пароль",
            "passwordShow": "Показать пароль",
            "previous": "Предыдущий",
            "previousPageLabel": "Предыдущая страница",
            "removeLabel": "Удалить",
            "rotateLeft": "Повернуть влево",
            "rotateRight": "Повернуть вправо",
            "rowsPerPageLabel": "Строк на странице",
            "saveEdit": "Сохранить правку",
            "scrollTop": "Прокрутить в начало",
            "selectAll": "Выбрать все строки",
            "selectLabel": "Выбрать",
            "selectRow": "Выбрать строку",
            "showFilterMenu": "Показать меню фильтра",
            "slide": "Слайд",
            "slideNumber": "{slideNumber}",
            "star": "1 звезда",
            "stars": "{star} звёзд",
            "trueLabel": "Верно",
            "unselectAll": "Отменить выбор всех строк",
            "unselectLabel": "Отменить выбор",
            "unselectRow": "Отменить выбор строки",
            "zoomImage": "Увеличить изображение",
            "zoomIn": "Увеличить",
            "zoomOut": "Уменьшить"
        },
    },
    theme: {
        preset: MyPreset,
        options: {
            darkModeSelector: '.dark',
            cssLayer: {
                name: 'primevue',
                order: 'tailwind-base, primevue, tailwind-utilities'
            }
        }
    }
});

app.mount('#app')
