# Тестовое задание для стажировки ВК

Публичное API доступно по ссылке https://vk-test-1.vercel.app/api/ \
Полное описание API находится в `api.json`

### Краткое описание 

##### Первый метод
GET https://vk-test-1.vercel.app/api/addEvent \
Принимает обязательные параметры name и status(0 или 1) \
Пример: https://vk-test-1.vercel.app/api/addEvent?name=api&status=0
##### Второй метод
POST https://vk-test-1.vercel.app/api/getEvents \
Принимает параметры в JSON формате. Массив `filter` содержит поля вида:
"имя поля": "значение" или "имя поля": [массив значений]. Фильтрация по дате происходит через поля `add_date_from` и `add_date_to` \
Массив `agr` содержит поле `type`. Значение `1` означает первую агрегацию, `1` - вторую и `3` - третью.
Если передано любое другое значение, то возвращаются все строки.

Пример:
```json
{
  "filter": {
    "id": 1,
    "user": ["::1", "1.1.1.1"],
    "add_date_from": "2023-05-06 07:49:11",
    "add_date_to": "2023-05-06 07:53:11"
  },
  "agr": {
    "type": 3
  }
}
```

P.S. \
Папка `api` и файл  `vercel.json` используется для только для деплоя приложения
