# OpenCart 3 — Customer dynamic shipping & payment

Two independent extensions for **OpenCart 3.x** that expose **per-customer** shipping and payment options from a single “logical” integration: you maintain **profiles** (name, internal code, API ID) and **bind** them to customers. At checkout, each linked customer only sees the options assigned to them.

> **Ελληνικά:** Δύο ξεχωριστά modules για OpenCart 3 — δυναμικοί τρόποι **αποστολής** και **πληρωμής** ανά πελάτη, με πίνακες προφίλ και δεσίματος, admin λίστες και αναζήτηση.

---

## Repository layout

| Path | Contents |
|------|----------|
| `shipping/upload/` | **Customer Dynamic Shipping** — copy into your OpenCart root (merge folders). |
| `payment/upload/` | **Customer Dynamic Payment** — same installation pattern. |

Each top-level folder is a **standalone** package: you can ship or install only shipping, only payment, or both.

Optional `install.xml` files (when present) are lightweight OCMOD metadata only — **no core file overrides** are required for these extensions.

---

## Features

### Shared (both modules)

- **Profiles** — `method_id`, display name, internal `code`, `api_id` (for ERP / external APIs).
- **Bindings** — many-to-many: one customer ↔ many profiles; one profile ↔ many customers.
- **Admin UI** — tabs: *Settings*, *Profiles*, *Customer links*; search on bindings (name, email, profile, code, API ID); pagination on bindings.
- **Languages** — `en-gb` and `el-gr` (admin + catalog where applicable).

### Shipping (`customer_dynamic_shipping`)

- Uses native **shipping** `getQuote()` with **sub-quotes** per profile.
- Checkout codes look like: `customer_dynamic_shipping.m{method_id}`.
- Settings: cost, tax class, geo zone, status, sort order.
- **Logged-in customers only** (guests see nothing from this method).

### Payment (`customer_dynamic_payment`)

- OpenCart normally shows **one** row per payment extension; this module adds **extra payment rows per profile** via a registered **event** on `catalog/model/checkout/payment_method/getMethods/after` → `extension/payment/customer_dynamic_payment/augment`.
- Checkout codes look like: `customer_dynamic_payment.m{method_id}`.
- Settings: minimum / maximum **order total** (0 = no limit), geo zone, status, sort order.
- **Logged-in customers only** (same idea as shipping).

---

## Requirements

- OpenCart **3.0.x** (tested conceptually against standard 3.x extension & event APIs).
- PHP **7.0+** (recommended: 7.4+).
- MySQL / MariaDB with `utf8` (as in default OpenCart installs).

---

## Installation

### 1. Copy files

Merge the contents of:

- `shipping/upload/` → your OpenCart root (so `admin/`, `catalog/` align with your shop).
- `payment/upload/` → same (if you use both).

Do **not** nest an extra `upload` folder inside the store; the folders inside `upload` must match the live paths.

### 2. Install extensions in admin

1. **Extensions → Extensions → Shipping** — install **Customer Dynamic Shipping** (code `customer_dynamic_shipping`), then open it and configure.
2. **Extensions → Extensions → Payment** — install **Customer Dynamic Payment** (code `customer_dynamic_payment`), then open it and configure.

Installing creates database tables and default settings. The **payment** module also registers the **event** in `oc_event` (OpenCart’s event table).

### 3. User group permissions

**System → Users → User Groups** — for the relevant admin group, grant **Access** and **Modify** to:

- `extension/shipping/customer_dynamic_shipping`
- `extension/payment/customer_dynamic_payment`

### 4. Cache / modifications

If you use OCMOD elsewhere, after any XML changes run **Extensions → Modifications → Refresh** and clear theme/cache as you normally would.

---

## Database

### Shipping

| Table | Purpose |
|-------|---------|
| `{prefix}customer_dynamic_shipping_method` | Profile definitions |
| `{prefix}customer_dynamic_shipping_bind` | `customer_id` + `method_id` |

### Payment

| Table | Purpose |
|-------|---------|
| `{prefix}customer_dynamic_payment_method` | Profile definitions |
| `{prefix}customer_dynamic_payment_bind` | `customer_id` + `method_id` |

**Uninstall** of each extension drops its tables (and for payment, removes its event rows). Take a backup if you need to keep history.

---

## How to use (admin)

1. **Profiles** — create rows with the title the customer sees, your ERP `code`, and optional `api_id`.
2. **Customer links** — pick a customer (autocomplete) and a profile; repeat for multiple profiles per customer.
3. **Search** — filter bindings by customer or profile fields.
4. **Settings** — enable the method, set geo zone / totals (payment) / cost & tax (shipping), and sort order.

---

## Technical notes

- **Order data** — the stored shipping/payment `code` includes the profile suffix (`m{id}`). Resolve full profile metadata (including `api_id`) in admin or via a small custom query joining `{prefix}customer_dynamic_*_method`.
- **Payment event** — if payment options do not appear, confirm the event exists in **Admin → Extensions → Events** (code `customer_dynamic_payment`) and that the trigger matches your OpenCart build (`catalog/model/checkout/payment_method/getMethods/after` on standard 3.x).
- **Older OpenCart 3.0.0** — if `deleteEventByCode` / event APIs differ, you may need a minor adjustment in the payment model `install()` / `uninstall()` methods.

---

## Compatibility hints

- Admin “back to extensions” links use **`marketplace/extension`** (OpenCart **3.0.3+**). On very old 3.0.x builds, switch breadcrumbs/cancel URLs to `extension/extension` with `type=shipping` or `type=payment` if needed.
- Subfolder installs (e.g. `https://shop.com/store/admin/`) — adjust autocomplete URLs in Twig if your admin URL is not the default `index.php` relative path.

---

## License

No license file is bundled by default. Add one (e.g. MIT) when you publish the repo if you want explicit terms.

---

## Contributing

Issues and pull requests are welcome: please mention **OpenCart exact version** (e.g. 3.0.3.8) and PHP version when reporting bugs.
