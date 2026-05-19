# FamsPay API — Internal Documentation

Base URL: `http://localhost:8000/api/v1`

All protected endpoints require: `Authorization: Bearer <token>`

---

## Authentication

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| POST | `/register` | ❌ | — | Register user baru |
| POST | `/login` | ❌ | — | Login, returns token |
| POST | `/logout` | ✅ | Any | Logout, invalidate token |
| GET | `/me` | ✅ | Any | Get profile user login |
| PATCH | `/me` | ✅ | Any | Update profile |

---

## Categories

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| GET | `/categories` | ✅ | Any | List semua kategori transaksi |

---

## Dashboard

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| GET | `/dashboard/personal` | ✅ | Any | Dashboard personal user |
| GET | `/dashboard/family` | ✅ | Admin | Dashboard agregat family |

---

## Family

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| POST | `/families` | ✅ | Any | Buat family baru |
| POST | `/families/join` | ✅ | Any | Bergabung ke family |
| GET | `/families/me` | ✅ | Any | Data family user login |
| GET | `/families/members` | ✅ | Admin | Daftar anggota family |
| PUT | `/families/{id}` | ✅ | Admin | Update nama family |
| PUT | `/families/members/{userId}/role` | ✅ | Admin | Update role anggota |
| DELETE | `/families/members/{userId}` | ✅ | Admin | Hapus anggota dari family |

---

## Monthly Limit

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| PUT | `/admin/members/{userId}/monthly-limit` | ✅ | Admin | Set/update monthly limit anggota |

**Body:**
```json
{
  "monthly_limit_base": 1000000,
  "period_month": "2026-05"
}
```

---

## Transactions

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| GET | `/transactions` | ✅ | Any | List transaksi (member: milik sendiri, admin: semua family) |
| POST | `/transactions/preview` | ✅ | Any | Preview efek transaksi baru |
| POST | `/transactions` | ✅ | Any | Buat transaksi baru |
| GET | `/transactions/{id}` | ✅ | Any | Detail transaksi |
| PUT | `/transactions/{id}/preview` | ✅ | Any | Preview efek edit transaksi |
| PUT | `/transactions/{id}` | ✅ | Any | Edit transaksi |
| DELETE | `/transactions/{id}` | ✅ | Any | Soft delete transaksi |

**Query params GET /transactions:**
- `date_from` — format: YYYY-MM-DD
- `date_to` — format: YYYY-MM-DD
- `type` — `income` | `expense`
- `category` — nama kategori

**Body POST /transactions:**
```json
{
  "type": "expense",
  "amount": 100000,
  "txn_date": "2026-05-10",
  "category_name": "Food",
  "note": "Makan siang",
  "confirm": false
}
```

**Notes:**
- `read_only: true` jika transaksi adalah goal-linked atau system transaction
- Kirim `confirm: true` jika ada warning (wallet/limit negatif)
- Goal-linked dan system transaction tidak bisa diedit/dihapus dari modul ini

---

## Goals

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| GET | `/goals` | ✅ | Any | List goal milik user login |
| POST | `/goals` | ✅ | Any | Buat goal baru |
| PUT | `/goals/{id}/archive` | ✅ | Any | Archive goal |
| POST | `/goals/{id}/allocate/preview` | ✅ | Any | Preview alokasi dana ke goal |
| POST | `/goals/{id}/allocate` | ✅ | Any | Alokasi dana ke goal |
| POST | `/goals/{id}/withdraw` | ✅ | Any | Withdraw dana dari goal |

**Query params GET /goals:**
- `include_archived=true` — tampilkan goal archived

**Body POST /goals:**
```json
{
  "title": "Liburan Bali",
  "target_amount": 5000000
}
```

**Body POST /goals/{id}/allocate:**
```json
{
  "amount": 500000,
  "confirm": false
}
```

**Notes:**
- Goal bersifat personal — admin tidak bisa lihat goal anggota lain
- Alokasi membuat system expense transaction (category: Savings)
- Withdraw membuat system income transaction (category: Goal Withdrawal)
- Goal auto-complete jika `current_amount >= target_amount`

---

## Permission Matrix

| Endpoint | Member | Admin |
|----------|--------|-------|
| Auth endpoints | ✅ | ✅ |
| Dashboard personal | ✅ | ✅ |
| Dashboard family | ❌ | ✅ |
| Family create/join | ✅ | ✅ |
| Family me | ✅ | ✅ |
| Family members list | ❌ | ✅ |
| Family management | ❌ | ✅ |
| Monthly limit | ❌ | ✅ |
| Transactions (own) | ✅ | ✅ |
| Transactions (family) | ❌ | ✅ |
| Goals (own) | ✅ | ✅ |
| Goals (others) | ❌ | ❌ |

---

## Business Rules

1. **Wallet balance** boleh negatif — user harus konfirmasi dengan `confirm: true`
2. **Remaining limit** boleh negatif — user harus konfirmasi dengan `confirm: true`
3. **Goal-linked transactions** tidak bisa diedit/dihapus dari modul transaksi
4. **System transactions** tidak bisa diedit/dihapus
5. **Last admin** tidak bisa di-remove atau di-demote dari family
6. **Goal** harus `active` untuk menerima alokasi
7. **Withdraw** tidak boleh melebihi `current_amount` goal
8. **txn_date** tidak boleh di masa depan
9. **amount** harus > 0
10. **period_month** format YYYY-MM
