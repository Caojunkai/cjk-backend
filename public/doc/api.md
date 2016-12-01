FORMAT: 1A

# 接口文档

CJK.CN接口文档

## 简介
- 客户端保存token，如果过期则客户端主动放弃，然后重新走登陆流程
- 服务器如果返回403状态码则需要客户端重新走登陆流程
- 所有成功的请求的http状态码都是 200
- 如果请求失败，先看返回的状态码文档中有没有定义,如果有的话取返回体中的msg字段
- 如果没有提示服务器错误
- 所有身份证号需要aes加密 近期确定公匙私匙
- 接口url由客户端拼接 baseUrl+url
- `token放在header的authorization字段中格式为Bearer{Token}`


# Group 用户类

## 用户 [/account/user]

### 用户注册 [POST]

`Header Authorization` `Bearer{Token}`

+ Request application/x-www-form-urlencoded

    - Headers
    
            Accept: application/json
            Content-Type: application/x-www-form-urlencoded
    - Body
    
            {
                "username":"必需",
                "password":"密码 | 必需",
                "email":"必需"
            }

+ Response 200 (application/json)

            {
                "code": 0,
                "msg": {
                    "id": 1,
                    "username": "fitz",
                    "email": "caojunkaiv@gmail.com",
                    "name": "fitz",
                    "real_name": "",
                    "avatar_url": "https://cn.gravatar.com/avatar/b6a8240560bb83b71659a37fb67e2708",
                    "use_gravatar": true,
                    "age": "0",
                    "gender": "unspecified",
                    "birthday": "",
                    "city": "",
                    "company": "",
                    "website": "",
                    "bio": "",
                    "site_admin": false,
                    "followers_count": "0",
                    "following_count": "0",
                    "topic_count": "0",
                    "article_count": "0",
                    "created_at": "2016-11-30 02:02:15",
                    "updated_at": "2016-11-30 02:02:15",
                    "jwt_token": {
                        "access_token ": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6XC9cL2FwaS5jYW9qdW5rYWkuY25cL2FjY291bnRcL3JlZ2lzdGVyIiwiaWF0IjoxNDgwNDcxMzM1LCJleHAiOjE0ODA0NzQ5MzUsIm5iZiI6MTQ4MDQ3MTMzNSwianRpIjoiZGMzMGVmYmE3NWIzNDA2ODgxZDMzMDEyOWMxMDAwNjUifQ.KhbEs5xGE-bOHYd7_eKwpD8Jz77K3_J5-tXjPRZHUpA",
                        "expires_in": 1480474935
                    },
                    "followed": true,
                    "configs": []
                }
            }
+ Response 401 (application/json)

            {
              "code": -1,
              "msg": "Validation Failed",
              "errors": [
                {
                  "field": "password",
                  "message": "The password must be between 6 and 32 characters."
                }
              ]
            }

### 获取用户信息 [GET]

`Header Authorization` `Bearer{Token}`

- Request application/x-www-form-urlencoded

    - Headers 
    
            Accept: application/json
            Content-Type: application/x-www-form-urlencoded

- Response 200 (application/json)

    - Body
    
            {
              "code": 0,
              "msg": {
                "id": 5,
                "username": "fitz",
                "email": "353290933@qq.com",
                "name": "fitz",
                "real_name": "",
                "avatar_url": "https://cn.gravatar.com/avatar/c612da2dba8fc89a6577324e21f71676",
                "use_gravatar": true,
                "age": 0,
                "gender": "unspecified",
                "birthday": "",
                "city": "",
                "company": "",
                "website": "",
                "bio": "",
                "site_admin": false,
                "followers_count": 0,
                "following_count": 0,
                "topic_count": 0,
                "article_count": 0,
                "created_at": "2016-11-28 15:33:34",
                "updated_at": "2016-11-29 12:48:40",
                "followed": true
              }
            }
            
- Response 401 (application/json)

    - Body
    
            {
              "code": -1,
              "msg": "令牌无效"
            }

### 修改用户信息 [PUT]

`Header Authorization` `Bearer{Token}`

- Request application/x-www-form-urlencoded

    - Headers
    
            Accept: application/json
            Content-Type: application/x-www-form-urlencoded
    - Body
   
            除了email password 外的信息         

- Response 200 (application/json)

            {
              "code": 0,
              "msg": {
                "id": 5,
                "username": "fitz",
                "email": "353290933@qq.com",
                "name": "fitz",
                "real_name": "",
                "avatar_url": "https://cn.gravatar.com/avatar/c612da2dba8fc89a6577324e21f71676",
                "use_gravatar": true,
                "age": 0,
                "gender": "unspecified",
                "birthday": "",
                "city": "",
                "company": "",
                "website": "",
                "bio": "",
                "site_admin": false,
                "followers_count": 0,
                "following_count": 0,
                "topic_count": 0,
                "article_count": 0,
                "created_at": "2016-11-28 15:33:34",
                "updated_at": "2016-11-29 12:48:40",
                "followed": true
              }
            }
- Response 422 (application/json)

            {
              "code": -1,
              "msg": "Validation Failed",
              "errors": [
                {
                  "field": "birthday",
                  "message": "The password must be between 6 and 32 characters."
                }
              ]
            }
            
- Response 401 (application/json)

    - Body
    
            {
              "code": -1,
              "msg": "令牌无效"
            }
                    
## 登录登出 [/account/user/token]

### 用户登录 [PUT]

`Header Authorization` `Bearer{Token}`

+ Request application/x-www-form-urlencoded

    - Headers
    
            Accept: application/json
            Content-Type: application/x-www-form-urlencoded
    - Body
    
            {
                "email":"必需",
                "passwprd":"必需"
            }

+ Response 200 (application/json)

            {
                "code": 0,
                "msg": {
                    "id": 1,
                    "username": "fitz",
                    "email": "caojunkaiv@gmail.com",
                    "name": "fitz",
                    "real_name": "",
                    "avatar_url": "https://cn.gravatar.com/avatar/b6a8240560bb83b71659a37fb67e2708",
                    "use_gravatar": true,
                    "age": "0",
                    "gender": "unspecified",
                    "birthday": "",
                    "city": "",
                    "company": "",
                    "website": "",
                    "bio": "",
                    "site_admin": false,
                    "followers_count": "0",
                    "following_count": "0",
                    "topic_count": "0",
                    "article_count": "0",
                    "created_at": "2016-11-30 02:02:15",
                    "updated_at": "2016-11-30 02:02:15",
                    "jwt_token": {
                        "access_token ": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6XC9cL2FwaS5jYW9qdW5rYWkuY25cL2FjY291bnRcL3JlZ2lzdGVyIiwiaWF0IjoxNDgwNDcxMzM1LCJleHAiOjE0ODA0NzQ5MzUsIm5iZiI6MTQ4MDQ3MTMzNSwianRpIjoiZGMzMGVmYmE3NWIzNDA2ODgxZDMzMDEyOWMxMDAwNjUifQ.KhbEs5xGE-bOHYd7_eKwpD8Jz77K3_J5-tXjPRZHUpA",
                        "expires_in": 1480474935
                    },
                    "followed": true,
                    "configs": []
                }
            }
            
- Response 422 (application/json)

            {
                "code": -1,
                "msg": "Validation Failed",
                "errors": [
                    {
                      "field": "password",
                      "message": "The password must be between 6 and 32 characters."
                    }
                ]
            }

- Response 401 (application/json)

            {
              "code": -1,
              "msg": "账号或密码不正确"
            }

### 用户退出 [DELETE]

`Header Authorization` `Bearer{Token}`

+ Request application/x-www-form-urlencoded

    - Headers
    
            Accept: application/json
            Content-Type: application/x-www-form-urlencoded
    
- Response 200

            {
                "code":0
            }

## 重置修改密码 [/account/pwd]

### 重置密码发送邮件 [POST]

- Request application/x-www-form-urlencoded

    - Headers
    
            Accept: application/json
            Content-Type: application/x-www-form-urlencoded
   
- Response 200 (application/json)
            
            异步发送不管成功失败都返回成功
            {
                "code":0
            }

### 重置密码 [PATCH]

- Request application/x-www-form-urlencoded

    - Headers
    
            Accept: application/json
            Content-Type: application/x-www-form-urlencoded
    - Body
            
            {
                "email":"必需",
                "verify":"验证码 | 必需"
                "password":"新密码 | 必需"
            }
   
- Response 200 (application/json)
            
            重走login
            
- Response 401 (application/json)
            
            {
                "code" : -1,
                "msg" : "验证码错误"
            }
            
- Response 500 (application/json)

            {
                "code" : -1,
                "msg" : "修改密码失败"
            }

### 修改密码 [PUT]

- Request application/x-www-form-urlencoded

    - Headers
    
            Accept: application/json
            Content-Type: application/x-www-form-urlencoded
            
    - Body
    
            {
                "oldPwd" : "旧密码 | 必需",
                "newPwd" : "新密码 | 必需
            }
   
- Response 200 (application/json)

            {
                "code" : 0,
                "msg" : "新Token"
            }
            
            