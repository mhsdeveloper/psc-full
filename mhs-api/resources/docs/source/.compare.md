---
title: API Reference

language_tabs:
- bash
- javascript

includes:

search: true

toc_footers:
- <a href='http://github.com/mpociot/documentarian'>Documentation Powered by Documentarian</a>
---
<!-- START_INFO -->
# Info

Welcome to the generated API reference.

<!-- END_INFO -->

#Aliases


APIs for managing aliases
<!-- START_cfd537a33df77d89af88a3218de5584e -->
## Browse

Retrieve a list of aliases

> Example request:

```bash
curl -X GET \
    -G "https://mhs-api.azurewebsites.net/api/v1/aliases" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/aliases"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "data": [
        {
            "id": 291,
            "family_name": "Hackett",
            "given_name": "Zack",
            "middle_name": null,
            "maiden_name": null,
            "suffix": "PhD",
            "title": null,
            "role": null,
            "type": "role",
            "public_notes": "Blanditiis qui animi et aspernatur nulla. Nisi aperiam dolores autem in et. Nostrum at omnis explicabo inventore in. Consequatur voluptatibus libero perspiciatis illo aliquam commodi et.",
            "staff_notes": "Ut amet quis alias non natus molestiae sequi. Cum et iure aperiam qui. Amet provident doloremque omnis officia dolorem consequatur quia."
        },
        {
            "id": 292,
            "family_name": "Doyle",
            "given_name": "Carole",
            "middle_name": null,
            "maiden_name": null,
            "suffix": "DVM",
            "title": null,
            "role": null,
            "type": "role",
            "public_notes": "Et magnam distinctio assumenda corporis dolores. Nihil facilis libero voluptatum est.",
            "staff_notes": "Est molestiae et harum molestiae saepe delectus debitis. Quos dolorum et omnis maxime. Ipsam quisquam eos rerum ipsum dolorum rerum consectetur."
        },
        {
            "id": 293,
            "family_name": "Hudson",
            "given_name": "Adam",
            "middle_name": null,
            "maiden_name": null,
            "suffix": "IV",
            "title": null,
            "role": null,
            "type": "role",
            "public_notes": "Soluta et nulla consequatur. Et itaque rem est.",
            "staff_notes": "Alias non est vel rerum aut est. Nam a et similique velit eos eos repellendus. Vitae placeat perspiciatis nihil non sapiente est quis praesentium. Cupiditate a repudiandae blanditiis eum autem."
        },
        {
            "id": 294,
            "family_name": "Willms",
            "given_name": "Cyril",
            "middle_name": null,
            "maiden_name": null,
            "suffix": "I",
            "title": null,
            "role": null,
            "type": "role",
            "public_notes": "Itaque neque ducimus et et. Dolorum architecto autem vitae molestiae ab sit. Maiores vel aut magni voluptatem consectetur.",
            "staff_notes": "Corrupti saepe porro provident suscipit. Qui velit magnam mollitia ipsa qui. Non necessitatibus dolores saepe natus eveniet cumque. Vel eligendi sit eius nemo dignissimos et."
        },
        {
            "id": 295,
            "family_name": "Wisoky",
            "given_name": "Kristina",
            "middle_name": null,
            "maiden_name": null,
            "suffix": "DDS",
            "title": null,
            "role": null,
            "type": "role",
            "public_notes": "Eius eos recusandae qui tempora. Sed consequatur harum dolor saepe. Vero corrupti sed est occaecati repellendus rerum.",
            "staff_notes": "Molestiae non aut sit quos nemo eligendi vitae modi. Vel unde occaecati velit excepturi dolores. Aliquid et necessitatibus vero enim dolorem. Nam et aut culpa reprehenderit."
        },
        {
            "id": 296,
            "family_name": "Reynolds",
            "given_name": "Edison",
            "middle_name": null,
            "maiden_name": null,
            "suffix": "MD",
            "title": null,
            "role": null,
            "type": "role",
            "public_notes": "Repellat enim est dolorem officia doloribus iure aliquid. Sed omnis iste a et. Rerum ea occaecati et quaerat molestiae.",
            "staff_notes": "Aut mollitia quis maxime. Voluptas quo similique voluptas vero. Molestiae et blanditiis dicta beatae et. Ut eum unde et ut quo."
        },
        {
            "id": 297,
            "family_name": "Green",
            "given_name": "Kameron",
            "middle_name": null,
            "maiden_name": null,
            "suffix": "Jr.",
            "title": null,
            "role": null,
            "type": "role",
            "public_notes": "Beatae voluptas eos officia dolorem omnis sed. Voluptatibus aut commodi sed est nisi. Excepturi animi suscipit rem qui iusto totam cupiditate.",
            "staff_notes": "Non maiores ut doloribus aut nam illum. Quo saepe sed fugit iusto rem. Facere et et ea quidem aut omnis commodi. Autem velit dolor explicabo consequatur aut velit."
        },
        {
            "id": 298,
            "family_name": "Farrell",
            "given_name": "Joshuah",
            "middle_name": null,
            "maiden_name": null,
            "suffix": "IV",
            "title": null,
            "role": null,
            "type": "role",
            "public_notes": "Nostrum tempora distinctio ex id dignissimos perspiciatis molestiae sit. Praesentium ut recusandae similique. Inventore repellendus modi molestias optio nulla blanditiis nemo corrupti.",
            "staff_notes": "Omnis fugiat tempore eveniet perferendis ut. Cupiditate illo iure non rerum ut. Sint minus fuga consequuntur dolor sit. Consequatur maiores eligendi accusamus."
        },
        {
            "id": 299,
            "family_name": "Mayert",
            "given_name": "Rachelle",
            "middle_name": null,
            "maiden_name": null,
            "suffix": "I",
            "title": null,
            "role": null,
            "type": "role",
            "public_notes": "Quidem eaque dolores qui ducimus. Rem officiis deleniti tempora quasi ea tenetur eligendi. Quaerat vero natus vitae amet est accusantium et. Fugiat cum fugiat expedita.",
            "staff_notes": "Odio consequatur est sit voluptates quas. Iure corrupti aut sit facilis totam ut ab aut. Ullam voluptatibus necessitatibus id earum ut. Adipisci iste maxime quibusdam consequuntur."
        },
        {
            "id": 300,
            "family_name": "Zemlak",
            "given_name": "Casandra",
            "middle_name": null,
            "maiden_name": null,
            "suffix": "V",
            "title": null,
            "role": null,
            "type": "role",
            "public_notes": "Eligendi ab earum odio. Rerum est hic voluptas fuga mollitia mollitia. Ad voluptatem libero molestias aperiam animi optio minus.",
            "staff_notes": "Omnis reiciendis eveniet facilis quam quae non. Ea doloribus et consectetur aliquam ullam culpa."
        }
    ],
    "links": {
        "first": "http:\/\/localhost?page=1",
        "last": "http:\/\/localhost?page=5",
        "prev": null,
        "next": "http:\/\/localhost?page=2"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 5,
        "path": "http:\/\/localhost",
        "per_page": 10,
        "to": 10,
        "total": 50
    }
}
```

### HTTP Request
`GET api/v1/aliases`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `per_page` |  optional  | optional Limit page results.
    `page` |  optional  | optional Page number to load:

<!-- END_cfd537a33df77d89af88a3218de5584e -->

<!-- START_7b56a1d63dd44cd0375d1a340d4f631e -->
## Read

Retrieve a specific alias

> Example request:

```bash
curl -X GET \
    -G "https://mhs-api.azurewebsites.net/api/v1/aliases/3" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/aliases/3"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "id": "3",
    "family_name": "Jefferson",
    "given_name": "Thomas",
    "middle_name": null,
    "maiden_name": null,
    "suffix": null,
    "title": null,
    "role": null,
    "type": "role",
    "public_notes": null,
    "staff_notes": null
}
```
> Example response (404):

```json
{
    "message": "No query results for model"
}
```

### HTTP Request
`GET api/v1/aliases/{id}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  required  | The ID of the Alias.

<!-- END_7b56a1d63dd44cd0375d1a340d4f631e -->

<!-- START_dcaf040745d3176c01ff334ee050609e -->
## Edit

> Example request:

```bash
curl -X PATCH \
    "https://mhs-api.azurewebsites.net/api/v1/aliases/3" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"name_id":1,"type":"role","family_name":"Buren","given_name":"Martin","middle_name":"Van","suffix":"Mr.","title":"President","role":"accusantium","public_notes":"tenetur","staff_notes":"qui"}'

```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/aliases/3"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name_id": 1,
    "type": "role",
    "family_name": "Buren",
    "given_name": "Martin",
    "middle_name": "Van",
    "suffix": "Mr.",
    "title": "President",
    "role": "accusantium",
    "public_notes": "tenetur",
    "staff_notes": "qui"
}

fetch(url, {
    method: "PATCH",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PATCH api/v1/aliases/{id}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  required  | The ID of the Alias.
#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `name_id` | integer |  optional  | optional The id of the name.
        `type` | string |  optional  | optional The type of alias.
        `family_name` | string |  optional  | optional The family name for the alias.
        `given_name` | string |  optional  | optional The given name for the alias.
        `middle_name` | string |  optional  | optional The middle name for the alias.
        `suffix` | string |  optional  | optional The suffix for the alias.
        `title` | string |  optional  | optional The title for the alias.
        `role` | string |  optional  | optional The role for the alias.
        `public_notes` | text |  optional  | optional The public notes for the alias.
        `staff_notes` | text |  optional  | optional The staff notes for the alias.
    
<!-- END_dcaf040745d3176c01ff334ee050609e -->

<!-- START_0e0a5bf73fdd990c73308a946ee2ac06 -->
## Add

> Example request:

```bash
curl -X POST \
    "https://mhs-api.azurewebsites.net/api/v1/aliases" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"name_id":3,"type":"role","family_name":"Buren","given_name":"Martin","middle_name":"Van","suffix":"Mr.","title":"President","role":"dolor","public_notes":"non","staff_notes":"quia"}'

```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/aliases"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name_id": 3,
    "type": "role",
    "family_name": "Buren",
    "given_name": "Martin",
    "middle_name": "Van",
    "suffix": "Mr.",
    "title": "President",
    "role": "dolor",
    "public_notes": "non",
    "staff_notes": "quia"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "id": "3",
    "family_name": "Jefferson",
    "given_name": "Thomas",
    "middle_name": null,
    "maiden_name": null,
    "suffix": null,
    "title": null,
    "role": null,
    "type": "role",
    "public_notes": null,
    "staff_notes": null
}
```

### HTTP Request
`POST api/v1/aliases`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `name_id` | integer |  required  | The id of the name.
        `type` | string |  required  | The type of alias.
        `family_name` | string |  required  | The family name for the alias.
        `given_name` | string |  optional  | optional The given name for the alias.
        `middle_name` | string |  optional  | optional The middle name for the alias.
        `suffix` | string |  optional  | optional The suffix for the alias.
        `title` | string |  optional  | optional The title for the alias.
        `role` | string |  optional  | optional The role for the alias.
        `public_notes` | text |  optional  | optional The public notes for the alias.
        `staff_notes` | text |  optional  | optional The staff notes for the alias.
    
<!-- END_0e0a5bf73fdd990c73308a946ee2ac06 -->

<!-- START_03b79615cdc57d7ec10e04ab8f0d0e25 -->
## Delete

Remove a specific alias

> Example request:

```bash
curl -X DELETE \
    "https://mhs-api.azurewebsites.net/api/v1/aliases/3" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/aliases/3"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/aliases/{id}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  required  | The ID of the Alias.

<!-- END_03b79615cdc57d7ec10e04ab8f0d0e25 -->

#Document


APIs for managing dopcuments
<!-- START_f9f46754ae9a48ce4e10a68541a0704e -->
## Browse

Retrieve a list of documents

> Example request:

```bash
curl -X GET \
    -G "https://mhs-api.azurewebsites.net/api/v1/documents" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/documents"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "data": [
        {
            "id": 5,
            "filename": "this is a test",
            "project_id": "63",
            "notes": null,
            "author": "Test Author",
            "document_date": null,
            "document_type": "test-document-type",
            "published": "0",
            "publish_date": "2020-08-29 00:00:00.000",
            "checked_out": "0",
            "steps": []
        },
        {
            "id": 6,
            "filename": "this is a test",
            "project_id": "63",
            "notes": null,
            "author": "Test Author",
            "document_date": null,
            "document_type": "test-document-type",
            "published": "0",
            "publish_date": "2020-08-29 00:00:00.000",
            "checked_out": "0",
            "steps": []
        },
        {
            "id": 7,
            "filename": "this is a test",
            "project_id": "63",
            "notes": null,
            "author": "Test Author",
            "document_date": null,
            "document_type": "test-document-type",
            "published": "0",
            "publish_date": "2020-08-29 00:00:00.000",
            "checked_out": "0",
            "steps": []
        },
        {
            "id": 8,
            "filename": "this is a test",
            "project_id": "63",
            "notes": null,
            "author": "Test Author",
            "document_date": null,
            "document_type": "test-document-type",
            "published": "0",
            "publish_date": "2020-08-29 00:00:00.000",
            "checked_out": "0",
            "steps": []
        },
        {
            "id": 9,
            "filename": "this is a test",
            "project_id": "63",
            "notes": null,
            "author": "Test Author",
            "document_date": null,
            "document_type": "test-document-type",
            "published": "0",
            "publish_date": "2020-08-29 00:00:00.000",
            "checked_out": "0",
            "steps": []
        },
        {
            "id": 10,
            "filename": "this is a test",
            "project_id": "63",
            "notes": null,
            "author": "Test Author",
            "document_date": null,
            "document_type": "test-document-type",
            "published": "0",
            "publish_date": "2020-08-29 00:00:00.000",
            "checked_out": "0",
            "steps": []
        },
        {
            "id": 11,
            "filename": "this is a test",
            "project_id": "63",
            "notes": null,
            "author": "Test Author",
            "document_date": null,
            "document_type": "test-document-type",
            "published": "0",
            "publish_date": "2020-08-29 00:00:00.000",
            "checked_out": "0",
            "steps": [
                {
                    "id": 5,
                    "name": "First Read",
                    "order": "1",
                    "project_id": "63",
                    "short_name": null,
                    "description": null,
                    "status": "1"
                },
                {
                    "id": 6,
                    "name": "Second Read",
                    "order": "1",
                    "project_id": "63",
                    "short_name": null,
                    "description": null,
                    "status": null
                },
                {
                    "id": 7,
                    "name": "Encoding",
                    "order": "1",
                    "project_id": "63",
                    "short_name": null,
                    "description": null,
                    "status": null
                },
                {
                    "id": 8,
                    "name": "Editorial Check",
                    "order": "1",
                    "project_id": "63",
                    "short_name": null,
                    "description": null,
                    "status": null
                }
            ]
        }
    ],
    "links": {
        "first": "http:\/\/localhost?page=1",
        "last": "http:\/\/localhost?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "path": "http:\/\/localhost",
        "per_page": 10,
        "to": 7,
        "total": 7
    }
}
```

### HTTP Request
`GET api/v1/documents`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `per_page` |  optional  | optional Limit page results.
    `page` |  optional  | optional Page number to load:

<!-- END_f9f46754ae9a48ce4e10a68541a0704e -->

<!-- START_05fbb9368027b9755fb85930c94d1a97 -->
## Read

Retrieve a specific document

> Example request:

```bash
curl -X GET \
    -G "https://mhs-api.azurewebsites.net/api/v1/documents/3" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/documents/3"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "id": "3",
    "filename": "this is a test",
    "project_id": "63",
    "notes": null,
    "author": "Test Author",
    "document_date": null,
    "document_type": "test-document-type",
    "published": "0",
    "publish_date": "2020-08-29 00:00:00.000",
    "checked_out": "0"
}
```
> Example response (404):

```json
{
    "message": "No query results for model"
}
```

### HTTP Request
`GET api/v1/documents/{id}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  required  | The ID of the Document.

<!-- END_05fbb9368027b9755fb85930c94d1a97 -->

<!-- START_3b1c5bcf8825e8f0027c1993de7a58f9 -->
## Edit

Update the specified Document

> Example request:

```bash
curl -X PATCH \
    "https://mhs-api.azurewebsites.net/api/v1/documents/3" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"name_id":1,"type":"role","family_name":"Buren","given_name":"Martin","middle_name":"Van","suffix":"Mr.","title":"President","role":"sunt","public_notes":"eos","staff_notes":"suscipit"}'

```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/documents/3"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name_id": 1,
    "type": "role",
    "family_name": "Buren",
    "given_name": "Martin",
    "middle_name": "Van",
    "suffix": "Mr.",
    "title": "President",
    "role": "sunt",
    "public_notes": "eos",
    "staff_notes": "suscipit"
}

fetch(url, {
    method: "PATCH",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PATCH api/v1/documents/{id}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  required  | The ID of the Alias.
#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `name_id` | integer |  optional  | optional The id of the name.
        `type` | string |  optional  | optional The type of alias.
        `family_name` | string |  optional  | optional The family name for the alias.
        `given_name` | string |  optional  | optional The given name for the alias.
        `middle_name` | string |  optional  | optional The middle name for the alias.
        `suffix` | string |  optional  | optional The suffix for the alias.
        `title` | string |  optional  | optional The title for the alias.
        `role` | string |  optional  | optional The role for the alias.
        `public_notes` | text |  optional  | optional The public notes for the alias.
        `staff_notes` | text |  optional  | optional The staff notes for the alias.
    
<!-- END_3b1c5bcf8825e8f0027c1993de7a58f9 -->

<!-- START_9a662d6576f482a2d605a8647f32f2fb -->
## Add

> Example request:

```bash
curl -X POST \
    "https://mhs-api.azurewebsites.net/api/v1/documents" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"name_id":3,"type":"role","family_name":"Buren","given_name":"Martin","middle_name":"Van","suffix":"Mr.","title":"President","role":"saepe","public_notes":"ad","staff_notes":"non"}'

```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/documents"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name_id": 3,
    "type": "role",
    "family_name": "Buren",
    "given_name": "Martin",
    "middle_name": "Van",
    "suffix": "Mr.",
    "title": "President",
    "role": "saepe",
    "public_notes": "ad",
    "staff_notes": "non"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "id": "3",
    "filename": "this is a test",
    "project_id": "63",
    "notes": null,
    "author": "Test Author",
    "document_date": null,
    "document_type": "test-document-type",
    "published": "0",
    "publish_date": "2020-08-29 00:00:00.000",
    "checked_out": "0"
}
```

### HTTP Request
`POST api/v1/documents`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `name_id` | integer |  required  | The id of the name.
        `type` | string |  required  | The type of alias.
        `family_name` | string |  required  | The family name for the alias.
        `given_name` | string |  optional  | optional The given name for the alias.
        `middle_name` | string |  optional  | optional The middle name for the alias.
        `suffix` | string |  optional  | optional The suffix for the alias.
        `title` | string |  optional  | optional The title for the alias.
        `role` | string |  optional  | optional The role for the alias.
        `public_notes` | text |  optional  | optional The public notes for the alias.
        `staff_notes` | text |  optional  | optional The staff notes for the alias.
    
<!-- END_9a662d6576f482a2d605a8647f32f2fb -->

<!-- START_ca86226ecaf750d353707394843b6755 -->
## Delete

Remove a specific document

> Example request:

```bash
curl -X DELETE \
    "https://mhs-api.azurewebsites.net/api/v1/documents/3" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/documents/3"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/documents/{id}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  required  | The ID of the document.

<!-- END_ca86226ecaf750d353707394843b6755 -->

<!-- START_0729550b636d050d0bbbeadb9d02b4ea -->
## Update Document Step

Update the specified Document Step

> Example request:

```bash
curl -X PATCH \
    "https://mhs-api.azurewebsites.net/api/v1/documents/1/steps" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"step_id":3,"status":2}'

```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/documents/1/steps"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "step_id": 3,
    "status": 2
}

fetch(url, {
    method: "PATCH",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PATCH api/v1/documents/{id}/steps`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `step_id` | integer |  required  | The id of the step.
        `status` | integer |  required  | The status of the step.
    
<!-- END_0729550b636d050d0bbbeadb9d02b4ea -->

<!-- START_60255e698446cbe0fa278b5f284d5719 -->
## Read

Retrieve a specific document

> Example request:

```bash
curl -X GET \
    -G "https://mhs-api.azurewebsites.net/api/v1/document-step/3" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/document-step/3"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "id": "3",
    "filename": "this is a test",
    "project_id": "63",
    "notes": null,
    "author": "Test Author",
    "document_date": null,
    "document_type": "test-document-type",
    "published": "0",
    "publish_date": "2020-08-29 00:00:00.000",
    "checked_out": "0"
}
```
> Example response (404):

```json
{
    "message": "No query results for model"
}
```

### HTTP Request
`GET api/v1/document-step/{id}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  required  | The ID of the Document.

<!-- END_60255e698446cbe0fa278b5f284d5719 -->

#Links


APIs for managing links
<!-- START_5abf710e926abfce98a2456aa6b223f5 -->
## Browse

Retrieve a list of links

> Example request:

```bash
curl -X GET \
    -G "https://mhs-api.azurewebsites.net/api/v1/links" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/links"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "data": [
        {
            "id": 389,
            "type": "source",
            "authority": "snac",
            "authority_id": "12345",
            "display_title": "dooley.com",
            "url": "http:\/\/hahn.com\/dicta-autem-voluptatem-eos-dolor-autem-necessitatibus-maxime-quia",
            "notes": "Et qui et est et aut."
        },
        {
            "id": 390,
            "type": "source",
            "authority": "snac",
            "authority_id": "12345",
            "display_title": "bogan.com",
            "url": "http:\/\/www.buckridge.info\/",
            "notes": "Quaerat et ut quibusdam."
        },
        {
            "id": 391,
            "type": "source",
            "authority": "snac",
            "authority_id": "12345",
            "display_title": "tromp.com",
            "url": "http:\/\/www.klein.org\/autem-quos-in-est-voluptatibus-qui-ex",
            "notes": "Vero enim aliquam modi."
        },
        {
            "id": 392,
            "type": "source",
            "authority": "snac",
            "authority_id": "12345",
            "display_title": "skiles.info",
            "url": "http:\/\/rodriguez.biz\/voluptas-blanditiis-sapiente-esse-fugit-commodi",
            "notes": "Deleniti ex error quae."
        },
        {
            "id": 393,
            "type": "source",
            "authority": "snac",
            "authority_id": "12345",
            "display_title": "skiles.com",
            "url": "http:\/\/hamill.org\/",
            "notes": "Voluptatem et rem at."
        },
        {
            "id": 394,
            "type": "source",
            "authority": "snac",
            "authority_id": "12345",
            "display_title": "kuhic.info",
            "url": "http:\/\/www.heller.biz\/dolor-molestiae-voluptatibus-nihil-velit-aut-quis.html",
            "notes": "Qui qui aut ipsa eum."
        },
        {
            "id": 395,
            "type": "source",
            "authority": "snac",
            "authority_id": "12345",
            "display_title": "okuneva.info",
            "url": "http:\/\/boyle.com\/",
            "notes": "Et et illum dicta ut."
        },
        {
            "id": 396,
            "type": "source",
            "authority": "snac",
            "authority_id": "12345",
            "display_title": "kuhic.biz",
            "url": "http:\/\/lynch.com\/",
            "notes": "Eum quae debitis harum."
        },
        {
            "id": 397,
            "type": "source",
            "authority": "snac",
            "authority_id": "12345",
            "display_title": "armstrong.info",
            "url": "http:\/\/www.langworth.biz\/amet-consectetur-qui-rerum-delectus",
            "notes": "Fuga et atque voluptas."
        },
        {
            "id": 398,
            "type": "source",
            "authority": "snac",
            "authority_id": "12345",
            "display_title": "oconnell.com",
            "url": "http:\/\/reilly.com\/voluptates-molestias-rerum-nisi-voluptates-voluptatem.html",
            "notes": "Quia amet quod sed et."
        }
    ],
    "links": {
        "first": "http:\/\/localhost?page=1",
        "last": "http:\/\/localhost?page=20",
        "prev": null,
        "next": "http:\/\/localhost?page=2"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 20,
        "path": "http:\/\/localhost",
        "per_page": 10,
        "to": 10,
        "total": 193
    }
}
```

### HTTP Request
`GET api/v1/links`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `per_page` |  optional  | optional Limit page results.
    `page` |  optional  | optional Page number to load:

<!-- END_5abf710e926abfce98a2456aa6b223f5 -->

<!-- START_61603de006810d4d80d39ef53695ac09 -->
## Read

Retrieve a specific link

> Example request:

```bash
curl -X GET \
    -G "https://mhs-api.azurewebsites.net/api/v1/links/3" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/links/3"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "id": "3",
    "linkable_id": "4",
    "linkable_type": "Models\\Subject",
    "type": "source",
    "authority": "snac",
    "authority_id": "12345",
    "display_title": "this is a link",
    "url": "www.yahoo.com",
    "notes": "n\/a"
}
```
> Example response (404):

```json
{
    "message": "No query results for model"
}
```

### HTTP Request
`GET api/v1/links/{id}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  required  | The ID of the Link.

<!-- END_61603de006810d4d80d39ef53695ac09 -->

<!-- START_b5208241d490e2668223c8fa6eed60c2 -->
## Edit

> Example request:

```bash
curl -X PATCH \
    "https://mhs-api.azurewebsites.net/api/v1/links/3" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"type":"source","authority":"snac","authority_id":"123456","display_title":"Click me","url":"www.yahoo.com","notes":"n\/a"}'

```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/links/3"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "type": "source",
    "authority": "snac",
    "authority_id": "123456",
    "display_title": "Click me",
    "url": "www.yahoo.com",
    "notes": "n\/a"
}

fetch(url, {
    method: "PATCH",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PATCH api/v1/links/{id}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  required  | The ID of the Link.
#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `type` | string |  optional  | optional The type of the link.
        `authority` | string |  optional  | optional The authority of the link.
        `authority_id` | string |  optional  | optional The authority id of the link.
        `display_title` | string |  optional  | optional The display title of the link.
        `url` | string |  optional  | optional The url of the link.
        `notes` | string |  optional  | optional The notes of the link.
    
<!-- END_b5208241d490e2668223c8fa6eed60c2 -->

<!-- START_4a3fbb251b3780a0b3a359c3276aa216 -->
## Add

Create a new link

> Example request:

```bash
curl -X POST \
    "https://mhs-api.azurewebsites.net/api/v1/links" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"type":"source","authority":"snac","authority_id":"123456","display_title":"Click me","url":"www.yahoo.com","notes":"n\/a","linkable_id":"quis","linkable_type":"voluptatem"}'

```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/links"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "type": "source",
    "authority": "snac",
    "authority_id": "123456",
    "display_title": "Click me",
    "url": "www.yahoo.com",
    "notes": "n\/a",
    "linkable_id": "quis",
    "linkable_type": "voluptatem"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "id": "3",
    "linkable_id": "4",
    "linkable_type": "Models\\Subject",
    "type": "source",
    "authority": "snac",
    "authority_id": "12345",
    "display_title": "this is a link",
    "url": "www.yahoo.com",
    "notes": "n\/a"
}
```

### HTTP Request
`POST api/v1/links`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `type` | string |  required  | The type of the link.
        `authority` | string |  required  | The authority of the link.
        `authority_id` | string |  required  | The authority id of the link.
        `display_title` | string |  required  | The display title of the link.
        `url` | string |  required  | The url of the link.
        `notes` | string |  optional  | optional The notes of the link.
        `linkable_id` | string |  optional  | optional
        `linkable_type` | string |  optional  | optional
    
<!-- END_4a3fbb251b3780a0b3a359c3276aa216 -->

<!-- START_3e24df97e7dbb5049e63f12aeb042e46 -->
## Delete

Remove a specific link

> Example request:

```bash
curl -X DELETE \
    "https://mhs-api.azurewebsites.net/api/v1/links/3" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/links/3"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/links/{id}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  required  | The ID of the Link.

<!-- END_3e24df97e7dbb5049e63f12aeb042e46 -->

#Lists


APIs for managing project lists
<!-- START_f1b8eb2943d95c92b42a3439d853d551 -->
## Browse

Retrieve a list of lists

> Example request:

```bash
curl -X GET \
    -G "https://mhs-api.azurewebsites.net/api/v1/lists" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/lists"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "data": [
        {
            "id": 30,
            "project_id": "65",
            "name": "repudiandae provident",
            "type": "subject",
            "description": null
        },
        {
            "id": 31,
            "project_id": "66",
            "name": "inventore rerum",
            "type": "subject",
            "description": null
        },
        {
            "id": 32,
            "project_id": "67",
            "name": "non minus",
            "type": "subject",
            "description": null
        },
        {
            "id": 33,
            "project_id": "123-456-789",
            "name": "associated subjects",
            "type": "subject",
            "description": null
        },
        {
            "id": 34,
            "project_id": "123-456-789",
            "name": "associated subjects",
            "type": "subject",
            "description": null
        }
    ],
    "links": {
        "first": "http:\/\/localhost?page=1",
        "last": "http:\/\/localhost?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "path": "http:\/\/localhost",
        "per_page": 10,
        "to": 5,
        "total": 5
    }
}
```

### HTTP Request
`GET api/v1/lists`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `per_page` |  optional  | optional Limit page results.
    `page` |  optional  | optional Page number to load:

<!-- END_f1b8eb2943d95c92b42a3439d853d551 -->

<!-- START_1b5803b0b970d515247e13c48bfe44b4 -->
## Read

Retrieve a specific list

> Example request:

```bash
curl -X GET \
    -G "https://mhs-api.azurewebsites.net/api/v1/lists/3" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/lists/3"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "id": "2",
    "project_id": "123-456-789",
    "name": "associated subjects",
    "type": "subject",
    "description": null
}
```
> Example response (404):

```json
{
    "message": "No query results for model"
}
```

### HTTP Request
`GET api/v1/lists/{id}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  required  | The ID of the List.

<!-- END_1b5803b0b970d515247e13c48bfe44b4 -->

<!-- START_1023d20bc3b0d7b7ba6ea1aecc22c4ca -->
## Edit

Update a specific list

> Example request:

```bash
curl -X PATCH \
    "https://mhs-api.azurewebsites.net/api/v1/lists/3" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"project_id":"123-456-789","name":"associated subjects","type":"subject"}'

```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/lists/3"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "project_id": "123-456-789",
    "name": "associated subjects",
    "type": "subject"
}

fetch(url, {
    method: "PATCH",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PATCH api/v1/lists/{id}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  required  | The ID of the List.
#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `project_id` | string |  optional  | optional The project id of the list.
        `name` | string |  optional  | optional The name of the list.
        `type` | string |  optional  | optional The type of the list.
    
<!-- END_1023d20bc3b0d7b7ba6ea1aecc22c4ca -->

<!-- START_f88b33dc550222c72ee0a4cf694a9a32 -->
## Add

Create a new list

> Example request:

```bash
curl -X POST \
    "https://mhs-api.azurewebsites.net/api/v1/lists" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"project_id":"123-456-789","name":"associated subjects","type":"subject"}'

```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/lists"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "project_id": "123-456-789",
    "name": "associated subjects",
    "type": "subject"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/lists`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `project_id` | string |  required  | The project id of the list.
        `name` | string |  required  | The name of the list.
        `type` | string |  required  | The type of the list.
    
<!-- END_f88b33dc550222c72ee0a4cf694a9a32 -->

<!-- START_1001bf95d9727bed33119c4e7901ecfd -->
## Delete

Remove a specific list

> Example request:

```bash
curl -X DELETE \
    "https://mhs-api.azurewebsites.net/api/v1/lists/3" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/lists/3"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/lists/{id}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  required  | The ID of the List.

<!-- END_1001bf95d9727bed33119c4e7901ecfd -->

<!-- START_47f6c20800b07aedb864e1a3f113762b -->
## Copy

Create a new list and copies associations from a specific list

> Example request:

```bash
curl -X POST \
    "https://mhs-api.azurewebsites.net/api/v1/lists/copy" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"project_id":"123-456-789","name":"associated subjects","type":"subject","list_id":"28"}'

```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/lists/copy"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "project_id": "123-456-789",
    "name": "associated subjects",
    "type": "subject",
    "list_id": "28"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/lists/copy`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `project_id` | string |  required  | The project id of the list.
        `name` | string |  required  | The name of the list.
        `type` | string |  required  | The type of the list.
        `list_id` | string |  required  | The list id to copy associations from.
    
<!-- END_47f6c20800b07aedb864e1a3f113762b -->

<!-- START_3a98c55a42d773ee9c643effe7f97e5a -->
## Name Toggle

Toggle a name for a specific list

> Example request:

```bash
curl -X PATCH \
    "https://mhs-api.azurewebsites.net/api/v1/lists/3/name" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"name_id":"28"}'

```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/lists/3/name"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name_id": "28"
}

fetch(url, {
    method: "PATCH",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PATCH api/v1/lists/{id}/name`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  required  | The ID of the List.
#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `name_id` | string |  required  | The name id to be toggled for the list.
    
<!-- END_3a98c55a42d773ee9c643effe7f97e5a -->

<!-- START_f7fd034b28ac31cab10a62a5d288efca -->
## Subject Toggle

Toggle a subject for a specific list

> Example request:

```bash
curl -X PATCH \
    "https://mhs-api.azurewebsites.net/api/v1/lists/3/subject" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"subject_id":"28"}'

```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/lists/3/subject"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "subject_id": "28"
}

fetch(url, {
    method: "PATCH",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PATCH api/v1/lists/{id}/subject`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  required  | The ID of the List.
#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `subject_id` | string |  required  | The subject id to be toggled for the list.
    
<!-- END_f7fd034b28ac31cab10a62a5d288efca -->

#Names


APIs for managing names
<!-- START_1e0430434c304b4be8f4ee1a04e6a251 -->
## Browse

Retrieve a list of names

> Example request:

```bash
curl -X GET \
    -G "https://mhs-api.azurewebsites.net/api/v1/names" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/names"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "data": [
        {
            "id": 661,
            "family_name": "Rau",
            "given_name": "Hunter",
            "middle_name": null,
            "maiden_name": null,
            "suffix": "III",
            "keywords": null,
            "date_of_birth": "1905-05-11",
            "date_of_death": "1900-07-01",
            "public_notes": "Est consequatur nulla aut alias. Reiciendis maxime atque assumenda et et. Asperiores doloremque optio ullam asperiores iure earum. Velit ullam sequi unde est consequatur.",
            "staff_notes": "Vero omnis nihil nostrum ipsa illo. Officia omnis voluptatum voluptatem id. Est doloribus occaecati vero odit. Voluptates et ea aliquam architecto.",
            "bio_filename": null,
            "name_key": "rau-hunter--1905-05-11",
            "aliases": [
                {
                    "id": 291,
                    "family_name": "Hackett",
                    "given_name": "Zack",
                    "middle_name": null,
                    "maiden_name": null,
                    "suffix": "PhD",
                    "title": null,
                    "role": null,
                    "type": "role",
                    "public_notes": "Blanditiis qui animi et aspernatur nulla. Nisi aperiam dolores autem in et. Nostrum at omnis explicabo inventore in. Consequatur voluptatibus libero perspiciatis illo aliquam commodi et.",
                    "staff_notes": "Ut amet quis alias non natus molestiae sequi. Cum et iure aperiam qui. Amet provident doloremque omnis officia dolorem consequatur quia."
                }
            ],
            "links": [
                {
                    "id": 389,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "dooley.com",
                    "url": "http:\/\/hahn.com\/dicta-autem-voluptatem-eos-dolor-autem-necessitatibus-maxime-quia",
                    "notes": "Et qui et est et aut."
                },
                {
                    "id": 390,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "bogan.com",
                    "url": "http:\/\/www.buckridge.info\/",
                    "notes": "Quaerat et ut quibusdam."
                },
                {
                    "id": 391,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "tromp.com",
                    "url": "http:\/\/www.klein.org\/autem-quos-in-est-voluptatibus-qui-ex",
                    "notes": "Vero enim aliquam modi."
                },
                {
                    "id": 392,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "skiles.info",
                    "url": "http:\/\/rodriguez.biz\/voluptas-blanditiis-sapiente-esse-fugit-commodi",
                    "notes": "Deleniti ex error quae."
                },
                {
                    "id": 393,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "skiles.com",
                    "url": "http:\/\/hamill.org\/",
                    "notes": "Voluptatem et rem at."
                },
                {
                    "id": 394,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "kuhic.info",
                    "url": "http:\/\/www.heller.biz\/dolor-molestiae-voluptatibus-nihil-velit-aut-quis.html",
                    "notes": "Qui qui aut ipsa eum."
                }
            ]
        },
        {
            "id": 662,
            "family_name": "Greenholt",
            "given_name": "Antonietta",
            "middle_name": null,
            "maiden_name": null,
            "suffix": "DVM",
            "keywords": null,
            "date_of_birth": "1897-04-25",
            "date_of_death": "1875-11-16",
            "public_notes": "Sit aut quaerat dolor nulla et autem. Officiis cum consequatur et fugiat totam porro ab.",
            "staff_notes": "Rerum corrupti voluptas debitis cum et. Quo magnam facilis quaerat dolorem recusandae. Aut neque et ut illum.",
            "bio_filename": null,
            "name_key": "greenholt-antonietta--1897-04-25",
            "aliases": [
                {
                    "id": 292,
                    "family_name": "Doyle",
                    "given_name": "Carole",
                    "middle_name": null,
                    "maiden_name": null,
                    "suffix": "DVM",
                    "title": null,
                    "role": null,
                    "type": "role",
                    "public_notes": "Et magnam distinctio assumenda corporis dolores. Nihil facilis libero voluptatum est.",
                    "staff_notes": "Est molestiae et harum molestiae saepe delectus debitis. Quos dolorum et omnis maxime. Ipsam quisquam eos rerum ipsum dolorum rerum consectetur."
                }
            ],
            "links": [
                {
                    "id": 395,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "okuneva.info",
                    "url": "http:\/\/boyle.com\/",
                    "notes": "Et et illum dicta ut."
                },
                {
                    "id": 396,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "kuhic.biz",
                    "url": "http:\/\/lynch.com\/",
                    "notes": "Eum quae debitis harum."
                },
                {
                    "id": 397,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "armstrong.info",
                    "url": "http:\/\/www.langworth.biz\/amet-consectetur-qui-rerum-delectus",
                    "notes": "Fuga et atque voluptas."
                }
            ]
        },
        {
            "id": 663,
            "family_name": "Pacocha",
            "given_name": "Jenifer",
            "middle_name": null,
            "maiden_name": null,
            "suffix": "DVM",
            "keywords": null,
            "date_of_birth": "1917-06-22",
            "date_of_death": "1902-11-13",
            "public_notes": "Rerum quia placeat adipisci omnis autem voluptates sint vel. Officia natus ea velit aspernatur aut quibusdam.",
            "staff_notes": "Quam nam quidem nihil sint est explicabo. Incidunt voluptates consequatur officiis eius nesciunt non tenetur. Ea labore rerum nisi tempore illum. Dolore eos veritatis sed id in.",
            "bio_filename": null,
            "name_key": "pacocha-jenifer--1917-06-22",
            "aliases": [
                {
                    "id": 293,
                    "family_name": "Hudson",
                    "given_name": "Adam",
                    "middle_name": null,
                    "maiden_name": null,
                    "suffix": "IV",
                    "title": null,
                    "role": null,
                    "type": "role",
                    "public_notes": "Soluta et nulla consequatur. Et itaque rem est.",
                    "staff_notes": "Alias non est vel rerum aut est. Nam a et similique velit eos eos repellendus. Vitae placeat perspiciatis nihil non sapiente est quis praesentium. Cupiditate a repudiandae blanditiis eum autem."
                }
            ],
            "links": [
                {
                    "id": 398,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "oconnell.com",
                    "url": "http:\/\/reilly.com\/voluptates-molestias-rerum-nisi-voluptates-voluptatem.html",
                    "notes": "Quia amet quod sed et."
                },
                {
                    "id": 399,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "kutch.com",
                    "url": "http:\/\/www.gleichner.com\/animi-ea-qui-quae-sint-voluptas-ducimus-possimus.html",
                    "notes": "Ut atque in non facere."
                },
                {
                    "id": 400,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "kiehn.com",
                    "url": "http:\/\/cassin.biz\/",
                    "notes": "Id sequi et molestiae."
                },
                {
                    "id": 401,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "powlowski.net",
                    "url": "http:\/\/eichmann.com\/",
                    "notes": "Dolores aut non est."
                }
            ]
        },
        {
            "id": 664,
            "family_name": "McClure",
            "given_name": "Maia",
            "middle_name": null,
            "maiden_name": null,
            "suffix": "Sr.",
            "keywords": null,
            "date_of_birth": "1892-05-09",
            "date_of_death": "1893-01-27",
            "public_notes": "Quos voluptatem delectus omnis neque. Ut neque placeat at laboriosam. Vitae dolore at labore est id assumenda.",
            "staff_notes": "Quam voluptatem quo numquam et quisquam quae id. Vel perferendis sit rerum fugit impedit. Veritatis iure aut ad sapiente at.",
            "bio_filename": null,
            "name_key": "mcclure-maia--1892-05-09",
            "aliases": [
                {
                    "id": 294,
                    "family_name": "Willms",
                    "given_name": "Cyril",
                    "middle_name": null,
                    "maiden_name": null,
                    "suffix": "I",
                    "title": null,
                    "role": null,
                    "type": "role",
                    "public_notes": "Itaque neque ducimus et et. Dolorum architecto autem vitae molestiae ab sit. Maiores vel aut magni voluptatem consectetur.",
                    "staff_notes": "Corrupti saepe porro provident suscipit. Qui velit magnam mollitia ipsa qui. Non necessitatibus dolores saepe natus eveniet cumque. Vel eligendi sit eius nemo dignissimos et."
                }
            ],
            "links": [
                {
                    "id": 402,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "hammes.biz",
                    "url": "http:\/\/beahan.net\/",
                    "notes": "Eius omnis enim quo et."
                },
                {
                    "id": 403,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "romaguera.org",
                    "url": "https:\/\/johns.com\/eveniet-excepturi-magnam-illo-aliquid-in-placeat-nihil-velit.html",
                    "notes": "Et labore et magnam."
                },
                {
                    "id": 404,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "funk.com",
                    "url": "http:\/\/buckridge.com\/",
                    "notes": "Nostrum tenetur in enim."
                },
                {
                    "id": 405,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "mcdermott.net",
                    "url": "http:\/\/www.mosciski.com\/debitis-est-velit-hic-dolorum-ut",
                    "notes": "Et saepe blanditiis sit."
                },
                {
                    "id": 406,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "sawayn.org",
                    "url": "http:\/\/robel.net\/inventore-qui-maiores-ab-sapiente-fugit-quos-deleniti",
                    "notes": "Voluptatem nihil sit et."
                }
            ]
        },
        {
            "id": 665,
            "family_name": "Cremin",
            "given_name": "Lafayette",
            "middle_name": null,
            "maiden_name": null,
            "suffix": "IV",
            "keywords": null,
            "date_of_birth": "1877-09-26",
            "date_of_death": "1867-06-14",
            "public_notes": "Ratione iste sapiente consectetur quas esse earum iusto. Qui repudiandae numquam quis deleniti sequi reiciendis nulla.",
            "staff_notes": "Repellat quidem ea magnam quis. Illum sunt quis at dolores sit. Delectus at officiis vel corporis.",
            "bio_filename": null,
            "name_key": "cremin-lafayette--1877-09-26",
            "aliases": [
                {
                    "id": 295,
                    "family_name": "Wisoky",
                    "given_name": "Kristina",
                    "middle_name": null,
                    "maiden_name": null,
                    "suffix": "DDS",
                    "title": null,
                    "role": null,
                    "type": "role",
                    "public_notes": "Eius eos recusandae qui tempora. Sed consequatur harum dolor saepe. Vero corrupti sed est occaecati repellendus rerum.",
                    "staff_notes": "Molestiae non aut sit quos nemo eligendi vitae modi. Vel unde occaecati velit excepturi dolores. Aliquid et necessitatibus vero enim dolorem. Nam et aut culpa reprehenderit."
                }
            ],
            "links": [
                {
                    "id": 407,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "pacocha.com",
                    "url": "http:\/\/www.boyle.net\/provident-vitae-commodi-vitae-dolor-rerum",
                    "notes": "Sint velit sunt ut."
                },
                {
                    "id": 408,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "wisozk.com",
                    "url": "http:\/\/gleichner.org\/odit-voluptatem-et-totam-rem-illo.html",
                    "notes": "Sit non id et soluta."
                },
                {
                    "id": 409,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "hickle.com",
                    "url": "http:\/\/www.barton.info\/pariatur-qui-quo-facere.html",
                    "notes": "Et mollitia id expedita."
                },
                {
                    "id": 410,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "jones.net",
                    "url": "http:\/\/bins.org\/",
                    "notes": "Et excepturi error et."
                },
                {
                    "id": 411,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "renner.com",
                    "url": "http:\/\/crist.com\/animi-voluptatibus-recusandae-commodi-vero-et-sed.html",
                    "notes": "Mollitia nam minus ex."
                }
            ]
        },
        {
            "id": 666,
            "family_name": "Thompson",
            "given_name": "Kassandra",
            "middle_name": null,
            "maiden_name": null,
            "suffix": "IV",
            "keywords": null,
            "date_of_birth": "1849-03-19",
            "date_of_death": "1862-04-15",
            "public_notes": "Dolore ipsa magnam vitae ut dolores sed reiciendis. Id facilis mollitia odit nihil incidunt ipsum cum. Blanditiis molestias voluptatem consequatur nisi iusto repellat.",
            "staff_notes": "Et ipsam qui quo sed ratione. Laboriosam aliquam nihil enim amet. In aspernatur sed qui. Distinctio voluptas velit rerum ipsum earum et eligendi.",
            "bio_filename": null,
            "name_key": "thompson-kassandra--1849-03-19",
            "aliases": [
                {
                    "id": 296,
                    "family_name": "Reynolds",
                    "given_name": "Edison",
                    "middle_name": null,
                    "maiden_name": null,
                    "suffix": "MD",
                    "title": null,
                    "role": null,
                    "type": "role",
                    "public_notes": "Repellat enim est dolorem officia doloribus iure aliquid. Sed omnis iste a et. Rerum ea occaecati et quaerat molestiae.",
                    "staff_notes": "Aut mollitia quis maxime. Voluptas quo similique voluptas vero. Molestiae et blanditiis dicta beatae et. Ut eum unde et ut quo."
                }
            ],
            "links": [
                {
                    "id": 412,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "moore.com",
                    "url": "http:\/\/quitzon.net\/eum-id-voluptatum-consequatur",
                    "notes": "Vitae odio illo maxime."
                },
                {
                    "id": 413,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "nolan.info",
                    "url": "https:\/\/hettinger.com\/quasi-maiores-quasi-mollitia-porro-id-eveniet-accusamus.html",
                    "notes": "Autem ut quos est quo."
                },
                {
                    "id": 414,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "auer.com",
                    "url": "http:\/\/www.yost.com\/",
                    "notes": "Hic non et aut magni."
                },
                {
                    "id": 415,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "klein.com",
                    "url": "http:\/\/rosenbaum.com\/",
                    "notes": "Animi et quia sit."
                },
                {
                    "id": 416,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "hill.biz",
                    "url": "http:\/\/mcdermott.org\/corporis-rerum-natus-asperiores-a-consequuntur-vel",
                    "notes": "Fugit et nihil ab."
                },
                {
                    "id": 417,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "quitzon.com",
                    "url": "http:\/\/www.schuster.com\/distinctio-ratione-quam-ex.html",
                    "notes": "Dolorem quo sint in in."
                }
            ]
        },
        {
            "id": 667,
            "family_name": "Streich",
            "given_name": "Rocio",
            "middle_name": null,
            "maiden_name": null,
            "suffix": "MD",
            "keywords": null,
            "date_of_birth": "1895-08-16",
            "date_of_death": "1909-01-04",
            "public_notes": "Aperiam nemo vel et ipsam. Deleniti in non numquam in sed. Totam ab accusantium iusto eveniet.",
            "staff_notes": "Eaque sapiente iure est rem reiciendis esse quia est. Est sit vel ullam sequi iure non ipsam. Earum eaque doloribus suscipit rerum non dolor. Blanditiis deserunt ad ratione deleniti eius.",
            "bio_filename": null,
            "name_key": "streich-rocio--1895-08-16",
            "aliases": [
                {
                    "id": 297,
                    "family_name": "Green",
                    "given_name": "Kameron",
                    "middle_name": null,
                    "maiden_name": null,
                    "suffix": "Jr.",
                    "title": null,
                    "role": null,
                    "type": "role",
                    "public_notes": "Beatae voluptas eos officia dolorem omnis sed. Voluptatibus aut commodi sed est nisi. Excepturi animi suscipit rem qui iusto totam cupiditate.",
                    "staff_notes": "Non maiores ut doloribus aut nam illum. Quo saepe sed fugit iusto rem. Facere et et ea quidem aut omnis commodi. Autem velit dolor explicabo consequatur aut velit."
                }
            ],
            "links": [
                {
                    "id": 418,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "simonis.com",
                    "url": "http:\/\/www.cruickshank.biz\/at-consectetur-sunt-eos-quisquam",
                    "notes": "Amet quo optio esse."
                },
                {
                    "id": 419,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "leffler.biz",
                    "url": "http:\/\/jerde.com\/non-quae-est-hic",
                    "notes": "Non ea alias delectus."
                }
            ]
        },
        {
            "id": 668,
            "family_name": "Prohaska",
            "given_name": "Ebba",
            "middle_name": null,
            "maiden_name": null,
            "suffix": "III",
            "keywords": null,
            "date_of_birth": "1863-03-02",
            "date_of_death": "1908-01-07",
            "public_notes": "Nisi laudantium pariatur neque ut iste. Eos omnis officiis animi doloremque aut corrupti et.",
            "staff_notes": "Vel mollitia officia sed. Placeat nemo voluptas libero. Corrupti magni voluptate nulla. Consectetur tempore repellendus cupiditate. Enim voluptatem iure tempora cumque ut.",
            "bio_filename": null,
            "name_key": "prohaska-ebba--1863-03-02",
            "aliases": [
                {
                    "id": 298,
                    "family_name": "Farrell",
                    "given_name": "Joshuah",
                    "middle_name": null,
                    "maiden_name": null,
                    "suffix": "IV",
                    "title": null,
                    "role": null,
                    "type": "role",
                    "public_notes": "Nostrum tempora distinctio ex id dignissimos perspiciatis molestiae sit. Praesentium ut recusandae similique. Inventore repellendus modi molestias optio nulla blanditiis nemo corrupti.",
                    "staff_notes": "Omnis fugiat tempore eveniet perferendis ut. Cupiditate illo iure non rerum ut. Sint minus fuga consequuntur dolor sit. Consequatur maiores eligendi accusamus."
                }
            ],
            "links": [
                {
                    "id": 420,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "anderson.com",
                    "url": "http:\/\/cronin.com\/",
                    "notes": "Facere ullam est ut."
                },
                {
                    "id": 421,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "bernhard.org",
                    "url": "http:\/\/flatley.org\/",
                    "notes": "Ut sed dolor dolor."
                },
                {
                    "id": 422,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "ullrich.com",
                    "url": "http:\/\/www.schowalter.info\/illo-nisi-libero-ea-ipsa",
                    "notes": "Iure impedit ut ut."
                },
                {
                    "id": 423,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "mcglynn.info",
                    "url": "http:\/\/tremblay.com\/laborum-magni-dignissimos-dolor-enim-dolorem",
                    "notes": "Laboriosam cum quas et."
                },
                {
                    "id": 424,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "satterfield.com",
                    "url": "https:\/\/aufderhar.com\/officiis-distinctio-similique-dolorum-facilis-sapiente.html",
                    "notes": "Vel hic laborum non et."
                },
                {
                    "id": 425,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "hamill.biz",
                    "url": "https:\/\/mcclure.com\/in-et-provident-dolorem-et-laudantium-eos-voluptate.html",
                    "notes": "Quia sunt sint et et."
                }
            ]
        },
        {
            "id": 669,
            "family_name": "Ankunding",
            "given_name": "Lucinda",
            "middle_name": null,
            "maiden_name": null,
            "suffix": "PhD",
            "keywords": null,
            "date_of_birth": "1862-03-14",
            "date_of_death": "1863-01-29",
            "public_notes": "Repellendus mollitia sed ipsam dolor quidem. Ad atque repellat voluptate impedit inventore. Esse ex odit consequuntur enim. Error atque autem molestiae laborum in.",
            "staff_notes": "Nisi rerum ea quisquam aut quis quasi et sunt. Omnis ipsam perferendis facere ut. Omnis maiores praesentium id praesentium ducimus minima odio.",
            "bio_filename": null,
            "name_key": "ankunding-lucinda--1862-03-14",
            "aliases": [
                {
                    "id": 299,
                    "family_name": "Mayert",
                    "given_name": "Rachelle",
                    "middle_name": null,
                    "maiden_name": null,
                    "suffix": "I",
                    "title": null,
                    "role": null,
                    "type": "role",
                    "public_notes": "Quidem eaque dolores qui ducimus. Rem officiis deleniti tempora quasi ea tenetur eligendi. Quaerat vero natus vitae amet est accusantium et. Fugiat cum fugiat expedita.",
                    "staff_notes": "Odio consequatur est sit voluptates quas. Iure corrupti aut sit facilis totam ut ab aut. Ullam voluptatibus necessitatibus id earum ut. Adipisci iste maxime quibusdam consequuntur."
                }
            ],
            "links": [
                {
                    "id": 426,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "pacocha.com",
                    "url": "https:\/\/larson.com\/soluta-recusandae-similique-veritatis-aut-quas-autem-cum.html",
                    "notes": "Non vitae qui ab quia."
                },
                {
                    "id": 427,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "rempel.com",
                    "url": "http:\/\/jaskolski.net\/laborum-quod-molestiae-ut-dolores-in-id.html",
                    "notes": "Similique eos non vitae."
                },
                {
                    "id": 428,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "rowe.com",
                    "url": "http:\/\/www.schowalter.org\/",
                    "notes": "Est ipsa quas et ad."
                },
                {
                    "id": 429,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "rogahn.com",
                    "url": "http:\/\/kuvalis.org\/alias-ipsa-fugit-saepe-exercitationem.html",
                    "notes": "Aut quam officiis quia."
                }
            ]
        },
        {
            "id": 670,
            "family_name": "Hoppe",
            "given_name": "Ole",
            "middle_name": null,
            "maiden_name": null,
            "suffix": "I",
            "keywords": null,
            "date_of_birth": "1849-08-24",
            "date_of_death": "1898-02-04",
            "public_notes": "Eius et consequuntur consequatur illo. Omnis qui id cum. Quod labore vel commodi odit voluptatem tempore.",
            "staff_notes": "Dolore tempora velit tempora quia modi voluptatem eligendi. Aut placeat itaque doloribus officia neque nulla est. Adipisci quaerat eaque velit placeat dolore. Dolorem consequuntur quisquam quasi et.",
            "bio_filename": null,
            "name_key": "hoppe-ole--1849-08-24",
            "aliases": [
                {
                    "id": 300,
                    "family_name": "Zemlak",
                    "given_name": "Casandra",
                    "middle_name": null,
                    "maiden_name": null,
                    "suffix": "V",
                    "title": null,
                    "role": null,
                    "type": "role",
                    "public_notes": "Eligendi ab earum odio. Rerum est hic voluptas fuga mollitia mollitia. Ad voluptatem libero molestias aperiam animi optio minus.",
                    "staff_notes": "Omnis reiciendis eveniet facilis quam quae non. Ea doloribus et consectetur aliquam ullam culpa."
                }
            ],
            "links": [
                {
                    "id": 430,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "ziemann.net",
                    "url": "http:\/\/watsica.info\/nobis-id-nihil-earum-quia-ipsam",
                    "notes": "Quia iste qui occaecati."
                },
                {
                    "id": 431,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "kling.com",
                    "url": "http:\/\/daugherty.com\/modi-deleniti-ab-non-repellat-dicta.html",
                    "notes": "Eum natus sed et."
                },
                {
                    "id": 432,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "von.com",
                    "url": "http:\/\/rempel.net\/",
                    "notes": "Veritatis sit cum et."
                },
                {
                    "id": 433,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "christiansen.com",
                    "url": "http:\/\/www.heidenreich.com\/",
                    "notes": "Qui animi beatae fuga."
                },
                {
                    "id": 434,
                    "type": "source",
                    "authority": "snac",
                    "authority_id": "12345",
                    "display_title": "kihn.org",
                    "url": "http:\/\/runte.com\/",
                    "notes": "Dicta laborum id et."
                }
            ]
        }
    ],
    "links": {
        "first": "http:\/\/localhost?page=1",
        "last": "http:\/\/localhost?page=5",
        "prev": null,
        "next": "http:\/\/localhost?page=2"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 5,
        "path": "http:\/\/localhost",
        "per_page": 10,
        "to": 10,
        "total": 50
    }
}
```

### HTTP Request
`GET api/v1/names`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `per_page` |  optional  | optional Limit page results.
    `page` |  optional  | optional Page number to load:

<!-- END_1e0430434c304b4be8f4ee1a04e6a251 -->

<!-- START_6f2a2899737a3c1cdc6f95308d1821db -->
## Read

Retrieve a specific name

> Example request:

```bash
curl -X GET \
    -G "https://mhs-api.azurewebsites.net/api/v1/names/3" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/names/3"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "id": "6",
    "family_name": "testing",
    "given_name": "another test",
    "maiden_name": "",
    "middle_name": "",
    "suffix": "",
    "keywords": "",
    "date_of_birth": "1968-04-23",
    "date_of_death": null,
    "public_notes": null,
    "staff_notes": null,
    "bio_filename": null,
    "aliases": [
        {
            "id": "6",
            "family_name": "testing",
            "given_name": "another test",
            "maiden_name": "",
            "middle_name": "",
            "suffix": "",
            "keywords": "",
            "date_of_birth": "1968-04-23",
            "date_of_death": null,
            "public_notes": null,
            "staff_notes": null,
            "bio_filename": null
        }
    ]
}
```
> Example response (404):

```json
{
    "message": "No query results for model"
}
```

### HTTP Request
`GET api/v1/names/{id}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  required  | The ID of the name.

<!-- END_6f2a2899737a3c1cdc6f95308d1821db -->

<!-- START_c6c410a18c77cd6b7edf849f896c9c10 -->
## Edit

> Example request:

```bash
curl -X PATCH \
    "https://mhs-api.azurewebsites.net/api/v1/names/3" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"family_name":"John","given_name":"Doe","maiden_name":"qui","middle_name":"nam","suffix":"reprehenderit","keywords":"repellendus","date_of_birth":"esse","date_of_death":"et","public_notes":"excepturi","staff_notes":"perferendis","bio_filename":"qui"}'

```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/names/3"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "family_name": "John",
    "given_name": "Doe",
    "maiden_name": "qui",
    "middle_name": "nam",
    "suffix": "reprehenderit",
    "keywords": "repellendus",
    "date_of_birth": "esse",
    "date_of_death": "et",
    "public_notes": "excepturi",
    "staff_notes": "perferendis",
    "bio_filename": "qui"
}

fetch(url, {
    method: "PATCH",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PATCH api/v1/names/{id}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  required  | The ID of the Name.
#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `family_name` | string |  optional  | optional The family name of the name.
        `given_name` | string |  optional  | optional The given name of the name.
        `maiden_name` | string |  optional  | optional The maiden name of the name.
        `middle_name` | string |  optional  | optional The middle name of the name.
        `suffix` | string |  optional  | optional The suffix of the name.
        `keywords` | string |  optional  | optional The keywords of the name.
        `date_of_birth` | string |  optional  | optional The date of birth of the name.
        `date_of_death` | string |  optional  | optional The date of death of the name.
        `public_notes` | string |  optional  | optional The public notes of the name.
        `staff_notes` | string |  optional  | optional The staff notes of the name.
        `bio_filename` | string |  optional  | optional The bio filename of the name.
    
<!-- END_c6c410a18c77cd6b7edf849f896c9c10 -->

<!-- START_3c0e59fe8a0631e8f1b4cb6e6fddb6ac -->
## Add

Create a new name

> Example request:

```bash
curl -X POST \
    "https://mhs-api.azurewebsites.net/api/v1/names" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"family_name":"John","given_name":"Doe","maiden_name":"odit","middle_name":"omnis","suffix":"autem","keywords":"iusto","date_of_birth":"quo","date_of_death":"voluptatum","public_notes":"odio","staff_notes":"aut","bio_filename":"quod"}'

```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/names"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "family_name": "John",
    "given_name": "Doe",
    "maiden_name": "odit",
    "middle_name": "omnis",
    "suffix": "autem",
    "keywords": "iusto",
    "date_of_birth": "quo",
    "date_of_death": "voluptatum",
    "public_notes": "odio",
    "staff_notes": "aut",
    "bio_filename": "quod"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "id": "6",
    "family_name": "testing",
    "given_name": "another test",
    "maiden_name": "",
    "middle_name": "",
    "suffix": "",
    "keywords": "",
    "date_of_birth": "1968-04-23",
    "date_of_death": null,
    "public_notes": null,
    "staff_notes": null,
    "bio_filename": null
}
```

### HTTP Request
`POST api/v1/names`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `family_name` | string |  optional  | optional The family name of the name.
        `given_name` | string |  optional  | optional The given name of the name.
        `maiden_name` | string |  optional  | optional The maiden name of the name.
        `middle_name` | string |  optional  | optional The middle name of the name.
        `suffix` | string |  optional  | optional The suffix of the name.
        `keywords` | string |  optional  | optional The keywords of the name.
        `date_of_birth` | string |  optional  | optional The date of birth of the name.
        `date_of_death` | string |  optional  | optional The date of death of the name.
        `public_notes` | string |  optional  | optional The public notes of the name.
        `staff_notes` | string |  optional  | optional The staff notes of the name.
        `bio_filename` | string |  optional  | optional The bio filename of the name.
    
<!-- END_3c0e59fe8a0631e8f1b4cb6e6fddb6ac -->

<!-- START_2f14d29bd7da846c5c64a1e07c08a19d -->
## Delete

Remove a specific name

> Example request:

```bash
curl -X DELETE \
    "https://mhs-api.azurewebsites.net/api/v1/names/3" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/names/3"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/names/{id}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  required  | The ID of the Name.

<!-- END_2f14d29bd7da846c5c64a1e07c08a19d -->

<!-- START_d47c6b12c59f75b55af064a67c3bb2e7 -->
## Browse Links

Retrieve a list of links for a specific name

> Example request:

```bash
curl -X GET \
    -G "https://mhs-api.azurewebsites.net/api/v1/names/3/links" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/names/3/links"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
[
    {
        "id": "3",
        "linkable_id": "4",
        "linkable_type": "Models\\Name",
        "type": "source",
        "authority": "snac",
        "authority_id": "12345",
        "display_title": "this is a link",
        "url": "www.yahoo.com",
        "notes": "n\/a"
    }
]
```
> Example response (404):

```json
{
    "message": "No query results for model"
}
```

### HTTP Request
`GET api/v1/names/{id}/links`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  required  | The ID of the name.

<!-- END_d47c6b12c59f75b55af064a67c3bb2e7 -->

#Projects


APIs for managing projects
<!-- START_d4bb0000cd4525b356d3f4e604741ee1 -->
## Browse

Retrieve a list of Projects

> Example request:

```bash
curl -X GET \
    -G "https://mhs-api.azurewebsites.net/api/v1/projects" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/projects"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "data": [
        {
            "id": 63,
            "project_id": "277-2-563-8662",
            "name": "id ducimus dicta",
            "description": "unde excepturi sint nisi molestias dolorem natus suscipit"
        },
        {
            "id": 64,
            "project_id": "275-8-381-4814",
            "name": "voluptatum quae sunt",
            "description": "suscipit fugit sed quidem esse consequuntur nesciunt quia"
        },
        {
            "id": 65,
            "project_id": "774-7-912-8836",
            "name": "impedit saepe et",
            "description": "suscipit odio id libero aut quasi ipsam itaque"
        },
        {
            "id": 66,
            "project_id": "843-5-228-9131",
            "name": "provident qui ab",
            "description": "similique doloribus aut et ut expedita dolor aut"
        },
        {
            "id": 67,
            "project_id": "864-2-225-2651",
            "name": "perspiciatis eos earum",
            "description": "qui architecto distinctio dolores nostrum cumque et itaque"
        }
    ],
    "links": {
        "first": "http:\/\/localhost?page=1",
        "last": "http:\/\/localhost?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "path": "http:\/\/localhost",
        "per_page": 10,
        "to": 5,
        "total": 5
    }
}
```

### HTTP Request
`GET api/v1/projects`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `per_page` |  optional  | optional Limit page results.
    `page` |  optional  | optional Page number to load:

<!-- END_d4bb0000cd4525b356d3f4e604741ee1 -->

<!-- START_c0c7035d6f07233f5023f3108d569268 -->
## Read

Retrieve the specified Project

> Example request:

```bash
curl -X GET \
    -G "https://mhs-api.azurewebsites.net/api/v1/projects/3" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/projects/3"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "id": "10",
    "project_id": "111-5-585-156",
    "name": "another test",
    "description": "testing"
}
```
> Example response (404):

```json
{
    "message": "No query results for model"
}
```

### HTTP Request
`GET api/v1/projects/{id}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  required  | The ID of the Project.

<!-- END_c0c7035d6f07233f5023f3108d569268 -->

<!-- START_fc1e4c1f87d2406ebf0d9350665d59e4 -->
## Edit

Update the specified Project

> Example request:

```bash
curl -X PATCH \
    "https://mhs-api.azurewebsites.net/api/v1/projects/3" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"project_id":"111-5-585-1566","name":"1800s Project","description":"quia"}'

```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/projects/3"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "project_id": "111-5-585-1566",
    "name": "1800s Project",
    "description": "quia"
}

fetch(url, {
    method: "PATCH",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PATCH api/v1/projects/{id}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  required  | The ID of the Project.
#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `project_id` | string |  optional  | optional The project id of the Project.
        `name` | string |  optional  | optional The name of the Project.
        `description` | string |  optional  | optional The description of the Project.
    
<!-- END_fc1e4c1f87d2406ebf0d9350665d59e4 -->

<!-- START_e832cdeb3d8617c57febfca7405a7263 -->
## Add

Create a new Project

> Example request:

```bash
curl -X POST \
    "https://mhs-api.azurewebsites.net/api/v1/projects" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"project_id":"111-5-585-1566","name":"1800s Project","description":"sit"}'

```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/projects"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "project_id": "111-5-585-1566",
    "name": "1800s Project",
    "description": "sit"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "id": "10",
    "project_id": "111-5-585-156",
    "name": "another test",
    "description": "testing"
}
```

### HTTP Request
`POST api/v1/projects`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `project_id` | string |  required  | The project id of the Project.
        `name` | string |  required  | The name of the Project.
        `description` | required |  optional  | optional The description of the Project.
    
<!-- END_e832cdeb3d8617c57febfca7405a7263 -->

<!-- START_85c1605eb5b3323aa82926b6add7c133 -->
## Delete

Remove the specified Project

> Example request:

```bash
curl -X DELETE \
    "https://mhs-api.azurewebsites.net/api/v1/projects/3" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/projects/3"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/projects/{id}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  required  | The ID of the Project.

<!-- END_85c1605eb5b3323aa82926b6add7c133 -->

<!-- START_57437c1e3cf93f8fe7e0aa04c814e6db -->
## Browse Lists

Retrieve lists for a Project

> Example request:

```bash
curl -X GET \
    -G "https://mhs-api.azurewebsites.net/api/v1/projects/1/lists" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/projects/1/lists"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (404):

```json
null
```

### HTTP Request
`GET api/v1/projects/{id}/lists`


<!-- END_57437c1e3cf93f8fe7e0aa04c814e6db -->

<!-- START_0f7b3565fe73767bf00a05c5bba607a6 -->
## Browse Names

Retrieve names for a Project

> Example request:

```bash
curl -X GET \
    -G "https://mhs-api.azurewebsites.net/api/v1/projects/1/names" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/projects/1/names"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (404):

```json
null
```

### HTTP Request
`GET api/v1/projects/{id}/names`


<!-- END_0f7b3565fe73767bf00a05c5bba607a6 -->

<!-- START_18b18ed73748bb800a68a8c6d976f55c -->
## Add Name

Add Name to a Project

> Example request:

```bash
curl -X POST \
    "https://mhs-api.azurewebsites.net/api/v1/projects/1/names" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/projects/1/names"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/projects/{id}/names`


<!-- END_18b18ed73748bb800a68a8c6d976f55c -->

<!-- START_a707c068926d2497cbb09820f30da35a -->
## Delete Name

Remove name from a Project

> Example request:

```bash
curl -X DELETE \
    "https://mhs-api.azurewebsites.net/api/v1/projects/1/names" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/projects/1/names"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/projects/{project_id}/names`


<!-- END_a707c068926d2497cbb09820f30da35a -->

<!-- START_8b9d872f016395ac37367b49ef763ef4 -->
## Browse Subjects

Retrieve subjects for a Project

> Example request:

```bash
curl -X GET \
    -G "https://mhs-api.azurewebsites.net/api/v1/projects/1/subjects" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/projects/1/subjects"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (404):

```json
null
```

### HTTP Request
`GET api/v1/projects/{id}/subjects`


<!-- END_8b9d872f016395ac37367b49ef763ef4 -->

<!-- START_0bb4aaae1fd132c511525815304354f2 -->
## Add Subject

Add Subject to a Project

> Example request:

```bash
curl -X POST \
    "https://mhs-api.azurewebsites.net/api/v1/projects/1/subjects" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/projects/1/subjects"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/v1/projects/{id}/subjects`


<!-- END_0bb4aaae1fd132c511525815304354f2 -->

<!-- START_1276459bba6bc7a6f4396200acdf2741 -->
## Delete Subject

Remove subject from a Project

> Example request:

```bash
curl -X DELETE \
    "https://mhs-api.azurewebsites.net/api/v1/projects/1/subjects" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/projects/1/subjects"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/projects/{project_id}/subjects`


<!-- END_1276459bba6bc7a6f4396200acdf2741 -->

#Step


APIs for managing dopcuments
<!-- START_46c5093d67e4e7dd0abba9cfd9e16999 -->
## Browse

Retrieve a list of steps

> Example request:

```bash
curl -X GET \
    -G "https://mhs-api.azurewebsites.net/api/v1/steps" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/steps"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "data": [
        {
            "id": 5,
            "name": "First Read",
            "order": "1",
            "project_id": "63",
            "short_name": null,
            "description": null
        },
        {
            "id": 6,
            "name": "Second Read",
            "order": "1",
            "project_id": "63",
            "short_name": null,
            "description": null
        },
        {
            "id": 7,
            "name": "Encoding",
            "order": "1",
            "project_id": "63",
            "short_name": null,
            "description": null
        },
        {
            "id": 8,
            "name": "Editorial Check",
            "order": "1",
            "project_id": "63",
            "short_name": null,
            "description": null
        }
    ],
    "links": {
        "first": "http:\/\/localhost?page=1",
        "last": "http:\/\/localhost?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "path": "http:\/\/localhost",
        "per_page": 10,
        "to": 4,
        "total": 4
    }
}
```

### HTTP Request
`GET api/v1/steps`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `per_page` |  optional  | optional Limit page results.
    `page` |  optional  | optional Page number to load:

<!-- END_46c5093d67e4e7dd0abba9cfd9e16999 -->

<!-- START_3b9481a6355fdc50fecde4f987b98c2a -->
## Read

Retrieve a specific step

> Example request:

```bash
curl -X GET \
    -G "https://mhs-api.azurewebsites.net/api/v1/steps/3" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/steps/3"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "id": "3",
    "name": "Step 1",
    "order": "1",
    "project_id": "11-124-11246",
    "short_name": null,
    "description": null
}
```
> Example response (404):

```json
{
    "message": "No query results for model"
}
```

### HTTP Request
`GET api/v1/steps/{id}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  required  | The ID of the Step.

<!-- END_3b9481a6355fdc50fecde4f987b98c2a -->

<!-- START_828a2e4e332e219c455e1945045afaf7 -->
## Edit

Update the specified Step

> Example request:

```bash
curl -X PATCH \
    "https://mhs-api.azurewebsites.net/api/v1/steps/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"project_id":"123-456-789","name":"Step 1","short_name":"aut","order":4,"description":"quia"}'

```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/steps/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "project_id": "123-456-789",
    "name": "Step 1",
    "short_name": "aut",
    "order": 4,
    "description": "quia"
}

fetch(url, {
    method: "PATCH",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PATCH api/v1/steps/{id}`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `project_id` | string |  required  | The project id of the list.
        `name` | string |  required  | The name of the step.
        `short_name` | string |  optional  | optional The short name of the step.
        `order` | integer |  required  | The order of the step.
        `description` | string |  optional  | optional The description of the step.
    
<!-- END_828a2e4e332e219c455e1945045afaf7 -->

<!-- START_102555aea6a991a9f6728e93531de053 -->
## Add

> Example request:

```bash
curl -X POST \
    "https://mhs-api.azurewebsites.net/api/v1/steps" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"project_id":"123-456-789","name":"Step 1","short_name":"cumque","order":19,"description":"est"}'

```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/steps"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "project_id": "123-456-789",
    "name": "Step 1",
    "short_name": "cumque",
    "order": 19,
    "description": "est"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "id": "3",
    "name": "Step 1",
    "order": "1",
    "project_id": "11-124-11246",
    "short_name": null,
    "description": null
}
```

### HTTP Request
`POST api/v1/steps`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `project_id` | string |  required  | The project id of the list.
        `name` | string |  required  | The name of the step.
        `short_name` | string |  optional  | optional The short name of the step.
        `order` | integer |  required  | The order of the step.
        `description` | string |  optional  | optional The description of the step.
    
<!-- END_102555aea6a991a9f6728e93531de053 -->

<!-- START_859912055c5b9cb7d2baab5edbb8bf25 -->
## Delete

Remove a specific step

> Example request:

```bash
curl -X DELETE \
    "https://mhs-api.azurewebsites.net/api/v1/steps/3" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/steps/3"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/steps/{id}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  required  | The ID of the step.

<!-- END_859912055c5b9cb7d2baab5edbb8bf25 -->

#Subjects


APIs for managing subjects
<!-- START_451cd228b1ef6fa32ccba39a38733061 -->
## Browse

Retrieve a list of Subjects

> Example request:

```bash
curl -X GET \
    -G "https://mhs-api.azurewebsites.net/api/v1/subjects" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/subjects"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "data": [
        {
            "id": 60,
            "subject_name": "quia quasi",
            "display_name": "nisi a",
            "staff_notes": null,
            "keywords": null,
            "loc": null
        },
        {
            "id": 61,
            "subject_name": "maxime animi",
            "display_name": "enim facere",
            "staff_notes": null,
            "keywords": null,
            "loc": null
        },
        {
            "id": 62,
            "subject_name": "temporibus voluptas",
            "display_name": "odio blanditiis",
            "staff_notes": null,
            "keywords": null,
            "loc": null
        },
        {
            "id": 63,
            "subject_name": "doloremque alias",
            "display_name": "voluptatem minus",
            "staff_notes": null,
            "keywords": null,
            "loc": null
        },
        {
            "id": 64,
            "subject_name": "eligendi eos",
            "display_name": "velit aut",
            "staff_notes": null,
            "keywords": null,
            "loc": null
        },
        {
            "id": 65,
            "subject_name": "aspernatur nesciunt",
            "display_name": "eaque aut",
            "staff_notes": null,
            "keywords": null,
            "loc": null
        },
        {
            "id": 66,
            "subject_name": "ut odio",
            "display_name": "et voluptatem",
            "staff_notes": null,
            "keywords": null,
            "loc": null
        },
        {
            "id": 67,
            "subject_name": "qui qui",
            "display_name": "consequatur eligendi",
            "staff_notes": null,
            "keywords": null,
            "loc": null
        },
        {
            "id": 68,
            "subject_name": "rerum adipisci",
            "display_name": "eum illum",
            "staff_notes": null,
            "keywords": null,
            "loc": null
        },
        {
            "id": 69,
            "subject_name": "enim ipsam",
            "display_name": "iste voluptas",
            "staff_notes": null,
            "keywords": null,
            "loc": null
        }
    ],
    "links": {
        "first": "http:\/\/localhost?page=1",
        "last": "http:\/\/localhost?page=2",
        "prev": null,
        "next": "http:\/\/localhost?page=2"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 2,
        "path": "http:\/\/localhost",
        "per_page": 10,
        "to": 10,
        "total": 14
    }
}
```

### HTTP Request
`GET api/v1/subjects`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `per_page` |  optional  | optional Limit page results.
    `page` |  optional  | optional Page number to load:

<!-- END_451cd228b1ef6fa32ccba39a38733061 -->

<!-- START_5e15402500604bd4622bbff7103366c8 -->
## Read

Retrieve the specified Subject

> Example request:

```bash
curl -X GET \
    -G "https://mhs-api.azurewebsites.net/api/v1/subjects/3" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/subjects/3"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "id": "3",
    "subject_name": "grandchild",
    "display_name": "this is a grandchild",
    "children": []
}
```
> Example response (404):

```json
{
    "message": "No query results for model"
}
```

### HTTP Request
`GET api/v1/subjects/{id}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  required  | The ID of the Subject.

<!-- END_5e15402500604bd4622bbff7103366c8 -->

<!-- START_7d8b60b8cc1fcf16b328476c1e8a5b05 -->
## Edit

Update the specified Subject

> Example request:

```bash
curl -X PATCH \
    "https://mhs-api.azurewebsites.net/api/v1/subjects/3" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"subject_name":"laborum","display_name":"reprehenderit","staff_notes":"asperiores","keywords":"architecto","loc":"quaerat","parent_id":"voluptate"}'

```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/subjects/3"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "subject_name": "laborum",
    "display_name": "reprehenderit",
    "staff_notes": "asperiores",
    "keywords": "architecto",
    "loc": "quaerat",
    "parent_id": "voluptate"
}

fetch(url, {
    method: "PATCH",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PATCH api/v1/subjects/{id}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  required  | The ID of the Subject.
#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `subject_name` | string |  optional  | optional The subject name of the Subject.
        `display_name` | string |  optional  | optional The display name of the Subject.
        `staff_notes` | string |  optional  | optional The staff notes of the Subject.
        `keywords` | string |  optional  | optional The keywords of the subject.
        `loc` | string |  optional  | optional The loc of the subject.
        `parent_id` | string |  optional  | optional The parent id of the subject.
    
<!-- END_7d8b60b8cc1fcf16b328476c1e8a5b05 -->

<!-- START_34aba826805a0fc069bf7672b1ceae89 -->
## Add

Create a new Subject

> Example request:

```bash
curl -X POST \
    "https://mhs-api.azurewebsites.net/api/v1/subjects" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"subject_name":"sit","display_name":"facilis","staff_notes":"laudantium","keywords":"eum","loc":"accusamus","parent_id":"earum"}'

```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/subjects"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "subject_name": "sit",
    "display_name": "facilis",
    "staff_notes": "laudantium",
    "keywords": "eum",
    "loc": "accusamus",
    "parent_id": "earum"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
null
```

### HTTP Request
`POST api/v1/subjects`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `subject_name` | string |  required  | The subject name of the Subject.
        `display_name` | string |  required  | The display name of the Subject.
        `staff_notes` | string |  optional  | optional The staff notes of the Subject.
        `keywords` | string |  optional  | optional The keywords of the subject.
        `loc` | string |  optional  | optional The loc of the subject.
        `parent_id` | string |  optional  | optional The parent id of the subject.
    
<!-- END_34aba826805a0fc069bf7672b1ceae89 -->

<!-- START_99bae6d5929e7514b230a0ae09d07d94 -->
## Delete

Remove the specified Subject

> Example request:

```bash
curl -X DELETE \
    "https://mhs-api.azurewebsites.net/api/v1/subjects/3" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/subjects/3"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/v1/subjects/{id}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  required  | The ID of the Subject.

<!-- END_99bae6d5929e7514b230a0ae09d07d94 -->

<!-- START_03fc9ea763efdfb330110f11036d027d -->
## Browse Links

Retrieve a list of links for a specific subject

> Example request:

```bash
curl -X GET \
    -G "https://mhs-api.azurewebsites.net/api/v1/subjects/3/links" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "https://mhs-api.azurewebsites.net/api/v1/subjects/3/links"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
[
    {
        "id": "3",
        "linkable_id": "4",
        "linkable_type": "Models\\Subject",
        "type": "source",
        "authority": "snac",
        "authority_id": "12345",
        "display_title": "this is a link",
        "url": "www.yahoo.com",
        "notes": "n\/a"
    }
]
```
> Example response (404):

```json
{
    "message": "No query results for model"
}
```

### HTTP Request
`GET api/v1/subjects/{id}/links`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  required  | The ID of the subject.

<!-- END_03fc9ea763efdfb330110f11036d027d -->


