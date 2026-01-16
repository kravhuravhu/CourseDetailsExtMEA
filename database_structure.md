
**Proposed Database Structure for Target System**

The database schema will be designed to reflect the hierarchical and relational nature of the XML schemas. Each major complex type will likely correspond to a table, and nested complex types or elements with `maxOccurs="unbounded"` will either become separate tables linked by foreign keys or be denormalized into the parent table if appropriate (e.g., simple lists of values).

Here's a breakdown by schema, focusing on the main entities:

---

### 1. `Personnel.xsd` (and `Persons.xsd` as `ErpPersonnel` extends `ErpPerson`)

This schema defines `Personnel` which contains `ErpPersonnel`. `ErpPersonnel` extends `ErpPerson`.

**Table: `PERSONNEL`**
*   `PERSONNEL_ID` (Primary Key, e.g., UUID or auto-increment)

**Table: `ERP_PERSONNEL`**
*   `ERP_PERSONNEL_ID` (Primary Key, e.g., UUID or auto-increment)
*   `PERSONNEL_ID` (Foreign Key to `PERSONNEL.PERSONNEL_ID`)
*   `ADMINISTRATION_INDICATOR` (BOOLEAN)
*   `DEEMED_START_DATE_TIME` (DATETIME)
*   `FINISH_DATE` (DATE)
*   `JOB_CODE` (VARCHAR) - *Note: Schema indicates this is obsoleted, but included for completeness if older data exists.*
*   `JOB_RESTRICTION_CODES` (VARCHAR) - *Note: Schema indicates this is obsoleted.*
*   `JOB_TITLE` (VARCHAR) - *Note: Schema indicates this is obsoleted.*
*   `KEY_PERSON_INDICATOR` (BOOLEAN)
*   `OVERTIME_ELIGIBLE_INDICATOR` (BOOLEAN)
*   `PAYMENT_METHOD` (VARCHAR) - *Note: Schema indicates this is obsoleted.*
*   `RESPONSIBILITY` (VARCHAR)
*   `START_DATE` (DATE)
*   `TRANSFER_BENEFITS_PAYABLE_INDICATOR` (BOOLEAN)

**Table: `ERP_PERSON` (Extended by `ERP_PERSONNEL`)**
*   `ERP_PERSON_ID` (Primary Key, e.g., UUID or auto-increment)
*   `ERP_PERSONNEL_ID` (Foreign Key to `ERP_PERSONNEL.ERP_PERSONNEL_ID`)
*   `MRID` (VARCHAR)
*   `ALIAS_NAME` (VARCHAR)
*   `DESCRIPTION` (VARCHAR)
*   `LOCAL_NAME` (VARCHAR)
*   `NAME` (VARCHAR)
*   `PATH_NAME` (VARCHAR)
*   `BIRTH_DATE_TIME` (DATETIME)
*   `CATEGORY` (VARCHAR)
*   `DEATH_DATE_TIME` (DATETIME)
*   `ETHNICITY` (VARCHAR)
*   `FIRST_NAME` (VARCHAR)
*   `GENDER` (VARCHAR)
*   `INITIALS` (VARCHAR)
*   `LAST_NAME` (VARCHAR)
*   `MAIDEN_NAME` (VARCHAR)
*   `MARITAL_STATUS` (VARCHAR)
*   `MARRIAGE_TYPE` (VARCHAR)
*   `M_NAME` (VARCHAR)
*   `NATIONALITY` (VARCHAR)
*   `NICKNAME` (VARCHAR)
*   `PREFIX` (VARCHAR)
*   `SPECIAL_NEEDS` (VARCHAR)
*   `SUFFIX` (VARCHAR)

**Related Tables for `ERP_PERSONNEL` / `ERP_PERSON` (from `Personnel.xsd` and `Persons.xsd`):**

*   **Table: `ACCESS_CARD`**
    *   `ACCESS_CARD_ID` (Primary Key)
    *   `ERP_PERSONNEL_ID` (Foreign Key to `ERP_PERSONNEL.ERP_PERSONNEL_ID`)
    *   `ACCESS_TYPE` (VARCHAR)
    *   `APPLICATION_DATE` (DATE)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `CATEGORY`, `CREATED_DATE_TIME`, `DOC_REMARKS`, `DOC_STATUS`, `DOC_STATUS_DATE`, `LAST_MODIFIED_DATE_TIME`, `REVISION_NUMBER`, `SUBJECT`, `TITLE` (Inherited from `Document`)
    *   `END_DATE_TIME`, `SCOPE_INFORMATION`, `SIGN_DATE`, `START_DATE_TIME` (Inherited from `Agreement`)

*   **Table: `ACCESS_CONTROL_AREA`**
    *   `ACCESS_CONTROL_AREA_ID` (Primary Key)
    *   `ACCESS_CARD_ID` (Foreign Key to `ACCESS_CARD.ACCESS_CARD_ID`)
    *   `ACCESS_CONTROL_AREA_NAME` (VARCHAR)
    *   `ACCESS_TYPE` (VARCHAR)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `CATEGORY`, `CODE`, `DIRECTION`, `GEO_INFO_REFERENCE`, `IS_POLYGON` (Inherited from `Location`)

*   **Table: `EMPLOYEE_BENEFIT`**
    *   `EMPLOYEE_BENEFIT_ID` (Primary Key)
    *   `ERP_PERSONNEL_ID` (Foreign Key to `ERP_PERSONNEL.ERP_PERSONNEL_ID`)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `CATEGORY`, `CREATED_DATE_TIME`, `DOC_REMARKS`, `DOC_STATUS`, `DOC_STATUS_DATE`, `LAST_MODIFIED_DATE_TIME`, `REVISION_NUMBER`, `SUBJECT`, `TITLE` (Inherited from `Document`)

*   **Table: `CARRY_ON_PERMIT`**
    *   `CARRY_ON_PERMIT_ID` (Primary Key)
    *   `ERP_PERSONNEL_ID` (Foreign Key to `ERP_PERSONNEL.ERP_PERSONNEL_ID`)
    *   `APPLICATION_DATE` (DATE)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `CATEGORY`, `CREATED_DATE_TIME`, `DOC_REMARKS`, `DOC_STATUS`, `DOC_STATUS_DATE`, `LAST_MODIFIED_DATE_TIME`, `REVISION_NUMBER`, `SUBJECT`, `TITLE` (Inherited from `Document`)
    *   `END_DATE_TIME`, `SCOPE_INFORMATION`, `SIGN_DATE`, `START_DATE_TIME` (Inherited from `Agreement`)

*   **Table: `CRAFT`**
    *   `CRAFT_ID` (Primary Key)
    *   `ERP_PERSONNEL_ID` (Foreign Key to `ERP_PERSONNEL.ERP_PERSONNEL_ID`)
    *   `CATEGORY` (VARCHAR)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)

*   **Table: `DEVELOPMENT_PLAN`**
    *   `DEVELOPMENT_PLAN_ID` (Primary Key)
    *   `ERP_PERSONNEL_ID` (Foreign Key to `ERP_PERSONNEL.ERP_PERSONNEL_ID`)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `CATEGORY`, `CREATED_DATE_TIME`, `DOC_REMARKS`, `DOC_STATUS`, `DOC_STATUS_DATE`, `LAST_MODIFIED_DATE_TIME`, `REVISION_NUMBER`, `SUBJECT`, `TITLE` (Inherited from `Document`)

*   **Table: `EMPLOYEE_APPRAISAL`**
    *   `EMPLOYEE_APPRAISAL_ID` (Primary Key)
    *   `ERP_PERSONNEL_ID` (Foreign Key to `ERP_PERSONNEL.ERP_PERSONNEL_ID`)
    *   `APPRAISAL_REASON` (VARCHAR)
    *   `APPRAISAL_SCHEDULE` (VARCHAR)
    *   `COMMENT` (VARCHAR)
    *   `RESULT_DATE` (DATETIME)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `CATEGORY`, `CREATED_DATE_TIME`, `DOC_REMARKS`, `DOC_STATUS`, `DOC_STATUS_DATE`, `LAST_MODIFIED_DATE_TIME`, `REVISION_NUMBER`, `SUBJECT`, `TITLE` (Inherited from `Document`)

*   **Table: `REMUNERATION_RECOMMENDATION`**
    *   `REMUNERATION_RECOMMENDATION_ID` (Primary Key)
    *   `EMPLOYEE_APPRAISAL_ID` (Foreign Key to `EMPLOYEE_APPRAISAL.EMPLOYEE_APPRAISAL_ID`)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)

*   **Table: `ERP_COMPETENCY`**
    *   `ERP_COMPETENCY_ID` (Primary Key)
    *   `ERP_PERSONNEL_ID` (Foreign Key to `ERP_PERSONNEL.ERP_PERSONNEL_ID`)
    *   `PROFICIENCY_LEVEL` (VARCHAR)
    *   `RELATED_EXPERIENCE_YEARS` (DECIMAL)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)

*   **Table: `KEY_PERFORMANCE_INDICATOR`**
    *   `KEY_PERFORMANCE_INDICATOR_ID` (Primary Key)
    *   `ERP_PERSONNEL_ID` (Foreign Key to `ERP_PERSONNEL.ERP_PERSONNEL_ID`)
    *   `SCORE` (FLOAT)
    *   `SOURCE` (VARCHAR)
    *   `TARGET` (VARCHAR) - *Note: Schema indicates this is obsoleted.*
    *   `VALUE` (VARCHAR)
    *   `WEIGHT` (FLOAT)
    *   `WEIGHTED_SCORE` (FLOAT)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `CATEGORY`, `CREATED_DATE_TIME`, `DOC_REMARKS`, `DOC_STATUS`, `DOC_STATUS_DATE`, `LAST_MODIFIED_DATE_TIME`, `REVISION_NUMBER`, `SUBJECT`, `TITLE` (Inherited from `Document`)

*   **Table: `KEY_PERFORMANCE_AREA`**
    *   `KEY_PERFORMANCE_AREA_ID` (Primary Key)
    *   `KEY_PERFORMANCE_INDICATOR_ID` (Foreign Key to `KEY_PERFORMANCE_INDICATOR.KEY_PERFORMANCE_INDICATOR_ID`)
    *   `SCORE` (FLOAT)
    *   `VALUE` (VARCHAR)
    *   `WEIGHT` (FLOAT)
    *   `WEIGHTED_SCORE` (FLOAT)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `CATEGORY`, `CREATED_DATE_TIME`, `DOC_REMARKS`, `DOC_STATUS`, `DOC_STATUS_DATE`, `LAST_MODIFIED_DATE_TIME`, `REVISION_NUMBER`, `SUBJECT`, `TITLE` (Inherited from `Document`)

*   **Table: `KEY_PERFORMANCE_INDICATOR_THRESHOLD`**
    *   `KEY_PERFORMANCE_INDICATOR_THRESHOLD_ID` (Primary Key)
    *   `KEY_PERFORMANCE_INDICATOR_ID` (Foreign Key to `KEY_PERFORMANCE_INDICATOR.KEY_PERFORMANCE_INDICATOR_ID`)
    *   `VALUE` (VARCHAR)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)

*   **Table: `LEAVE`**
    *   `LEAVE_ID` (Primary Key)
    *   `ERP_PERSONNEL_ID` (Foreign Key to `ERP_PERSONNEL.ERP_PERSONNEL_ID`)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `CATEGORY`, `CREATED_DATE_TIME`, `DOC_REMARKS`, `DOC_STATUS`, `DOC_STATUS_DATE`, `LAST_MODIFIED_DATE_TIME`, `REVISION_NUMBER`, `SUBJECT`, `TITLE` (Inherited from `Document`)

*   **Table: `MEMBERSHIP`**
    *   `MEMBERSHIP_ID` (Primary Key)
    *   `ERP_PERSONNEL_ID` (Foreign Key to `ERP_PERSONNEL.ERP_PERSONNEL_ID`)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `CATEGORY`, `CREATED_DATE_TIME`, `DOC_REMARKS`, `DOC_STATUS`, `DOC_STATUS_DATE`, `LAST_MODIFIED_DATE_TIME`, `REVISION_NUMBER`, `SUBJECT`, `TITLE` (Inherited from `Document`)

*   **Table: `PAYROLL`**
    *   `PAYROLL_ID` (Primary Key)
    *   `ERP_PERSONNEL_ID` (Foreign Key to `ERP_PERSONNEL.ERP_PERSONNEL_ID`)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `CATEGORY`, `CREATED_DATE_TIME`, `DOC_REMARKS`, `DOC_STATUS`, `DOC_STATUS_DATE`, `LAST_MODIFIED_DATE_TIME`, `REVISION_NUMBER`, `SUBJECT`, `TITLE` (Inherited from `Document`)

*   **Table: `PERFORMANCE_CONTRACT`**
    *   `PERFORMANCE_CONTRACT_ID` (Primary Key)
    *   `ERP_PERSONNEL_ID` (Foreign Key to `ERP_PERSONNEL.ERP_PERSONNEL_ID`)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `CATEGORY`, `CREATED_DATE_TIME`, `DOC_REMARKS`, `DOC_STATUS`, `DOC_STATUS_DATE`, `LAST_MODIFIED_DATE_TIME`, `REVISION_NUMBER`, `SUBJECT`, `TITLE` (Inherited from `Document`)

*   **Table: `PERMIT_REQUEST`**
    *   `PERMIT_REQUEST_ID` (Primary Key)
    *   `ERP_PERSONNEL_ID` (Foreign Key to `ERP_PERSONNEL.ERP_PERSONNEL_ID`)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `CATEGORY`, `CREATED_DATE_TIME`, `DOC_REMARKS`, `DOC_STATUS`, `DOC_STATUS_DATE`, `LAST_MODIFIED_DATE_TIME`, `REVISION_NUMBER`, `SUBJECT`, `TITLE` (Inherited from `Document`)

*   **Table: `PERSONNEL_ACTION_RECORD`**
    *   `PERSONNEL_ACTION_RECORD_ID` (Primary Key)
    *   `ERP_PERSONNEL_ID` (Foreign Key to `ERP_PERSONNEL.ERP_PERSONNEL_ID`)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `CATEGORY`, `CREATED_DATE_TIME`, `DOC_REMARKS`, `DOC_STATUS`, `DOC_STATUS_DATE`, `LAST_MODIFIED_DATE_TIME`, `REVISION_NUMBER`, `SUBJECT`, `TITLE` (Inherited from `Document`)

*   **Table: `PERSONNEL_GROUP`**
    *   `PERSONNEL_GROUP_ID` (Primary Key)
    *   `ERP_PERSONNEL_ID` (Foreign Key to `ERP_PERSONNEL.ERP_PERSONNEL_ID`)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `CATEGORY`, `CREATED_DATE_TIME`, `DOC_REMARKS`, `DOC_STATUS`, `DOC_STATUS_DATE`, `LAST_MODIFIED_DATE_TIME`, `REVISION_NUMBER`, `SUBJECT`, `TITLE` (Inherited from `Document`)

*   **Table: `POSITION`**
    *   `POSITION_ID` (Primary Key)
    *   `ERP_PERSONNEL_ID` (Foreign Key to `ERP_PERSONNEL.ERP_PERSONNEL_ID`)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `CATEGORY`, `CREATED_DATE_TIME`, `DOC_REMARKS`, `DOC_STATUS`, `DOC_STATUS_DATE`, `LAST_MODIFIED_DATE_TIME`, `REVISION_NUMBER`, `SUBJECT`, `TITLE` (Inherited from `Document`)

*   **Table: `EMPLOYEE_REMUNERATION`**
    *   `EMPLOYEE_REMUNERATION_ID` (Primary Key)
    *   `ERP_PERSONNEL_ID` (Foreign Key to `ERP_PERSONNEL.ERP_PERSONNEL_ID`)
    *   `EFFECTIVE_START_DATE` (DATETIME)
    *   `END_DATE` (DATETIME)
    *   `PAYMENT_METHOD` (VARCHAR)
    *   `REMUNERATION_RATE_INTERVAL` (VARCHAR)
    *   `REMUNERATION_TYPE` (VARCHAR)
    *   `AMOUNT_VALUE` (DECIMAL)
    *   `AMOUNT_MULTIPLIER` (VARCHAR)
    *   `AMOUNT_UNIT` (VARCHAR)
    *   `REMUNERATION_PERCENTAGE` (DECIMAL)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `CATEGORY`, `CREATED_DATE_TIME`, `DOC_REMARKS`, `DOC_STATUS`, `DOC_STATUS_DATE`, `LAST_MODIFIED_DATE_TIME`, `REVISION_NUMBER`, `SUBJECT`, `TITLE` (Inherited from `Document`)

*   **Table: `RETIREMENT`**
    *   `RETIREMENT_ID` (Primary Key)
    *   `ERP_PERSONNEL_ID` (Foreign Key to `ERP_PERSONNEL.ERP_PERSONNEL_ID`)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `CATEGORY`, `CREATED_DATE_TIME`, `DOC_REMARKS`, `DOC_STATUS`, `DOC_STATUS_DATE`, `LAST_MODIFIED_DATE_TIME`, `REVISION_NUMBER`, `SUBJECT`, `TITLE` (Inherited from `Document`)

*   **Table: `SKILL`**
    *   `SKILL_ID` (Primary Key)
    *   `ERP_PERSONNEL_ID` (Foreign Key to `ERP_PERSONNEL.ERP_PERSONNEL_ID`)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `CATEGORY`, `CREATED_DATE_TIME`, `DOC_REMARKS`, `DOC_STATUS`, `DOC_STATUS_DATE`, `LAST_MODIFIED_DATE_TIME`, `REVISION_NUMBER`, `SUBJECT`, `TITLE` (Inherited from `Document`)

*   **Table: `TRAVEL_PRIVILEGE`**
    *   `TRAVEL_PRIVILEGE_ID` (Primary Key)
    *   `ERP_PERSONNEL_ID` (Foreign Key to `ERP_PERSONNEL.ERP_PERSONNEL_ID`)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `CATEGORY`, `CREATED_DATE_TIME`, `DOC_REMARKS`, `DOC_STATUS`, `DOC_STATUS_DATE`, `LAST_MODIFIED_DATE_TIME`, `REVISION_NUMBER`, `SUBJECT`, `TITLE` (Inherited from `Document`)

---

### 2. `Organisations.xsd`

This schema defines `Organisations` which contains `ErpOrganisation`.

**Table: `ORGANISATIONS_COLLECTION`**
*   `ORGANISATIONS_COLLECTION_ID` (Primary Key)

**Table: `ERP_ORGANISATION`**
*   `ERP_ORGANISATION_ID` (Primary Key)
*   `ORGANISATIONS_COLLECTION_ID` (Foreign Key to `ORGANISATIONS_COLLECTION.ORGANISATIONS_COLLECTION_ID`)
*   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
*   `BEE_RATING` (VARCHAR)
*   `CATEGORY` (VARCHAR)
*   `CODE` (VARCHAR)
*   `COMPANY_REGISTRATION_NO` (VARCHAR)
*   `GOVERNMENT_ID` (VARCHAR)
*   `INDUSTRY_ID` (VARCHAR)
*   `IS_COST_CENTER` (BOOLEAN)
*   `IS_PROFIT_CENTER` (BOOLEAN)
*   `MODE` (VARCHAR)
*   `OPT_OUT` (BOOLEAN)
*   `VALUE_ADDED_TAX_ID` (VARCHAR)

**Related Tables for `ERP_ORGANISATION`:**

*   **Table: `ELECTRONIC_ADDRESS`**
    *   `ELECTRONIC_ADDRESS_ID` (Primary Key)
    *   `ERP_ORGANISATION_ID` (Foreign Key to `ERP_ORGANISATION.ERP_ORGANISATION_ID`)
    *   `EMAIL` (VARCHAR)
    *   `LAN` (VARCHAR)
    *   `PASSWORD` (VARCHAR) - *Note: Schema indicates this is obsoleted.*
    *   `RADIO` (VARCHAR)
    *   `SEQUENCE_NUMBER` (INTEGER)
    *   `USAGE` (VARCHAR)
    *   `USER_ID` (VARCHAR) - *Note: Schema indicates this is obsoleted.*
    *   `WEB` (VARCHAR)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)

*   **Table: `ERP_TELEPHONE_NUMBER`**
    *   `ERP_TELEPHONE_NUMBER_ID` (Primary Key)
    *   `ERP_ORGANISATION_ID` (Foreign Key to `ERP_ORGANISATION.ERP_ORGANISATION_ID`)
    *   `AREA_CODE` (VARCHAR)
    *   `CITY_CODE` (VARCHAR)
    *   `COUNTRY_CODE` (VARCHAR)
    *   `EXTENSION` (VARCHAR)
    *   `LOCAL_NUMBER` (VARCHAR)
    *   `SEQUENCE_NUMBER` (INTEGER)
    *   `TRANSMISSION_TYPE` (VARCHAR)
    *   `USAGE` (VARCHAR)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)

*   **Table: `BANK_DATA`**
    *   `BANK_DATA_ID` (Primary Key)
    *   `ERP_ORGANISATION_ID` (Foreign Key to `ERP_ORGANISATION.ERP_ORGANISATION_ID`)
    *   `BIC` (VARCHAR)
    *   `BRANCH_CODE` (VARCHAR)
    *   `IBAN` (VARCHAR)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)

*   **Table: `CUSTOMER_DATA`**
    *   `CUSTOMER_DATA_ID` (Primary Key)
    *   `ERP_ORGANISATION_ID` (Foreign Key to `ERP_ORGANISATION.ERP_ORGANISATION_ID`)
    *   `HAS_SPECIAL_NEEDS` (BOOLEAN)
    *   `KIND` (VARCHAR)
    *   `PUC_NUMBER` (VARCHAR)
    *   `SPECIAL_NEEDS` (VARCHAR)
    *   `VIP` (BOOLEAN)
    *   `VIP_DESCRIPTION` (VARCHAR)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)

*   **Table: `SUPPLIER_DATA`**
    *   `SUPPLIER_DATA_ID` (Primary Key)
    *   `ERP_ORGANISATION_ID` (Foreign Key to `ERP_ORGANISATION.ERP_ORGANISATION_ID`)
    *   `ISSUER_IDENTIFICATION_NUMBER` (VARCHAR)
    *   `KIND` (VARCHAR)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)

*   **Table: `TAX_INFORMATION`**
    *   `TAX_INFORMATION_ID` (Primary Key)
    *   `ERP_ORGANISATION_ID` (Foreign Key to `ERP_ORGANISATION.ERP_ORGANISATION_ID`)
    *   `TAX_NUMBER` (VARCHAR)
    *   `TAX_REFERENCE_NUMBER` (VARCHAR)
    *   `TAX_TYPE` (VARCHAR)

*   **Table: `ERP_BANK_ACCOUNT`**
    *   `ERP_BANK_ACCOUNT_ID` (Primary Key)
    *   `OWNING_ORGANISATION_ID` (Foreign Key to `ERP_ORGANISATION.ERP_ORGANISATION_ID` for `OwnedErpBankAccounts`)
    *   `PROVIDING_ORGANISATION_ID` (Foreign Key to `ERP_ORGANISATION.ERP_ORGANISATION_ID` for `ProvidedErpBankAccounts`)
    *   `ACCOUNT_NUMBER` (VARCHAR)
    *   `BANK_ABA` (VARCHAR)
    *   `BRANCH_CODE` (VARCHAR)
    *   `BRANCH_NAME` (VARCHAR)
    *   `SEQUENCE_NUMBER` (INTEGER)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `CATEGORY`, `CREATED_DATE_TIME`, `DOC_REMARKS`, `DOC_STATUS`, `DOC_STATUS_DATE`, `LAST_MODIFIED_DATE_TIME`, `REVISION_NUMBER`, `SUBJECT`, `TITLE` (Inherited from `Document`)

*   **Table: `SERVICE_DELIVERY_POINT`**
    *   `SERVICE_DELIVERY_POINT_ID` (Primary Key)
    *   `ERP_ORGANISATION_ID` (Foreign Key to `ERP_ORGANISATION.ERP_ORGANISATION_ID`)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)

---

### 3. `Locations.xsd`

This schema defines `Locations` which contains `Location`.

**Table: `LOCATIONS_COLLECTION`**
*   `LOCATIONS_COLLECTION_ID` (Primary Key)

**Table: `LOCATION`**
*   `LOCATION_ID` (Primary Key)
*   `LOCATIONS_COLLECTION_ID` (Foreign Key to `LOCATIONS_COLLECTION.LOCATIONS_COLLECTION_ID`)
*   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
*   `CATEGORY` (VARCHAR)
*   `CODE` (VARCHAR)
*   `DIRECTION` (VARCHAR)
*   `GEO_INFO_REFERENCE` (VARCHAR)
*   `IS_POLYGON` (BOOLEAN)

**Related Tables for `LOCATION`:**

*   **Table: `GML_COORDINATE_SYSTEM`**
    *   `GML_COORDINATE_SYSTEM_ID` (Primary Key)
    *   `LOCATIONS_COLLECTION_ID` (Foreign Key to `LOCATIONS_COLLECTION.LOCATIONS_COLLECTION_ID`)
    *   `POSITION_UNIT_NAME` (VARCHAR)
    *   `SCALE` (VARCHAR)
    *   `X_MAX` (VARCHAR)
    *   `X_MIN` (VARCHAR)
    *   `Y_MAX` (VARCHAR)
    *   `Y_MIN` (VARCHAR)
    *   `Z_MAX` (VARCHAR)
    *   `Z_MIN` (VARCHAR)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)

*   **Table: `GML_LINE_GEOMETRY`**
    *   `GML_LINE_GEOMETRY_ID` (Primary Key)
    *   `LOCATIONS_COLLECTION_ID` (Foreign Key to `LOCATIONS_COLLECTION.LOCATIONS_COLLECTION_ID`)
    *   `SOURCE_SIDE` (VARCHAR)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `CATEGORY`, `CODE`, `DIRECTION`, `GEO_INFO_REFERENCE`, `IS_POLYGON` (Inherited from `Location`)

*   **Table: `GML_POINT_GEOMETRY`**
    *   `GML_POINT_GEOMETRY_ID` (Primary Key)
    *   `LOCATIONS_COLLECTION_ID` (Foreign Key to `LOCATIONS_COLLECTION.LOCATIONS_COLLECTION_ID`)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `CATEGORY`, `CODE`, `DIRECTION`, `GEO_INFO_REFERENCE`, `IS_POLYGON` (Inherited from `Location`)

*   **Table: `GML_POLYGON_GEOMETRY`**
    *   `GML_POLYGON_GEOMETRY_ID` (Primary Key)
    *   `LOCATIONS_COLLECTION_ID` (Foreign Key to `LOCATIONS_COLLECTION.LOCATIONS_COLLECTION_ID`)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `CATEGORY`, `CODE`, `DIRECTION`, `GEO_INFO_REFERENCE`, `IS_POLYGON` (Inherited from `Location`)

*   **Table: `ZONE`**
    *   `ZONE_ID` (Primary Key)
    *   `LOCATIONS_COLLECTION_ID` (Foreign Key to `LOCATIONS_COLLECTION.LOCATIONS_COLLECTION_ID`)
    *   `KIND` (VARCHAR)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `CATEGORY`, `CODE`, `DIRECTION`, `GEO_INFO_REFERENCE`, `IS_POLYGON` (Inherited from `Location`)

*   **Table: `ACTIVITY_RECORD`**
    *   `ACTIVITY_RECORD_ID` (Primary Key)
    *   `LOCATION_ID` (Foreign Key to `LOCATION.LOCATION_ID`)
    *   `CATEGORY` (VARCHAR)
    *   `CREATED_DATE_TIME` (DATETIME)
    *   `REASON` (VARCHAR)
    *   `SEVERITY` (VARCHAR)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)

*   **Table: `DIMENSIONS_INFO`**
    *   `DIMENSIONS_INFO_ID` (Primary Key)
    *   `LOCATION_ID` (Foreign Key to `LOCATION.LOCATION_ID`)
    *   `ORIENTATION` (VARCHAR)
    *   `SIZE_DEPTH_VALUE` (FLOAT)
    *   `SIZE_DEPTH_MULTIPLIER` (VARCHAR)
    *   `SIZE_DEPTH_UNIT` (VARCHAR)
    *   `SIZE_DIAMETER_VALUE` (FLOAT)
    *   `SIZE_DIAMETER_MULTIPLIER` (VARCHAR)
    *   `SIZE_DIAMETER_UNIT` (VARCHAR)
    *   `SIZE_LENGTH_VALUE` (FLOAT)
    *   `SIZE_LENGTH_MULTIPLIER` (VARCHAR)
    *   `SIZE_LENGTH_UNIT` (VARCHAR)
    *   `SIZE_WIDTH_VALUE` (FLOAT)
    *   `SIZE_WIDTH_MULTIPLIER` (VARCHAR)
    *   `SIZE_WIDTH_UNIT` (VARCHAR)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)

*   **Table: `ELECTRONIC_ADDRESS_LOCATION`** (Linking table for ElectronicAddress to Location)
    *   `ELECTRONIC_ADDRESS_ID` (Foreign Key to `ELECTRONIC_ADDRESS.ELECTRONIC_ADDRESS_ID`)
    *   `LOCATION_ID` (Foreign Key to `LOCATION.LOCATION_ID`)
    *   (Composite Primary Key: `ELECTRONIC_ADDRESS_ID`, `LOCATION_ID`)

*   **Table: `POSTAL_ADDRESS`**
    *   `POSTAL_ADDRESS_ID` (Primary Key)
    *   `LOCATION_ID` (Foreign Key to `LOCATION.LOCATION_ID`)
    *   `PO_BOX` (VARCHAR)
    *   `PO_BOX_TYPE` (VARCHAR)
    *   `ADDRESS_GENERAL` (VARCHAR)
    *   `CITY` (VARCHAR)
    *   `CITY_SUB_DIVISION_NAME` (VARCHAR)
    *   `COUNTRY` (VARCHAR)
    *   `COUNTRY_SUB_DIVISION_CODE` (VARCHAR)
    *   `POSTAL_CODE` (VARCHAR)
    *   `SECTION` (VARCHAR)
    *   `STATE_OR_PROVINCE` (VARCHAR)
    *   `TOWN_CODE` (VARCHAR)
    *   `WITHIN_CITY_LIMITS` (BOOLEAN)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `FORMAT_CODE`, `SEQUENCE_NUMBER` (Inherited from `ErpAddress`)

*   **Table: `STREET_ADDRESS`**
    *   `STREET_ADDRESS_ID` (Primary Key)
    *   `LOCATION_ID` (Foreign Key to `LOCATION.LOCATION_ID`)
    *   `BUILDING_NAME` (VARCHAR)
    *   `STREET_NAME` (VARCHAR)
    *   `STREET_NUMBER` (VARCHAR)
    *   `STREET_PREFIX` (VARCHAR)
    *   `STREET_SUFFIX` (VARCHAR)
    *   `STREET_TYPE` (VARCHAR)
    *   `SUITE_NUMBER` (VARCHAR)
    *   `ADDRESS_GENERAL` (VARCHAR)
    *   `CITY` (VARCHAR)
    *   `CITY_SUB_DIVISION_NAME` (VARCHAR)
    *   `COUNTRY` (VARCHAR)
    *   `COUNTRY_SUB_DIVISION_CODE` (VARCHAR)
    *   `POSTAL_CODE` (VARCHAR)
    *   `SECTION` (VARCHAR)
    *   `STATE_OR_PROVINCE` (VARCHAR)
    *   `TOWN_CODE` (VARCHAR)
    *   `WITHIN_CITY_LIMITS` (BOOLEAN)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `FORMAT_CODE`, `SEQUENCE_NUMBER` (Inherited from `ErpAddress`)

*   **Table: `ERP_PERSON_LOC_ROLE`**
    *   `ERP_PERSON_LOC_ROLE_ID` (Primary Key)
    *   `LOCATION_ID` (Foreign Key to `LOCATION.LOCATION_ID`)
    *   `ERP_PERSON_ID` (Foreign Key to `ERP_PERSON.ERP_PERSON_ID`)
    *   `CATEGORY` (VARCHAR)
    *   `PRIVILEGE` (VARCHAR)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)

*   **Table: `GML_POSITION`**
    *   `GML_POSITION_ID` (Primary Key)
    *   `LOCATION_ID` (Foreign Key to `LOCATION.LOCATION_ID`)
    *   `SEQUENCE_NUMBER` (INTEGER)
    *   `X_POSITION` (VARCHAR)
    *   `Y_POSITION` (VARCHAR)
    *   `Z_POSITION` (VARCHAR)
    *   `GML_COORDINATE_SYSTEM_REF` (VARCHAR) - *This would likely be a foreign key to GML_COORDINATE_SYSTEM if it were a direct reference, but it's a string 'ref' in the XSD.*

*   **Table: `HAZARD`**
    *   `HAZARD_ID` (Primary Key)
    *   `LOCATION_ID` (Foreign Key to `LOCATION.LOCATION_ID`)
    *   `CATEGORY` (VARCHAR)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)

*   **Table: `LAND_PROPERTY`**
    *   `LAND_PROPERTY_ID` (Primary Key)
    *   `LOCATION_ID` (Foreign Key to `LOCATION.LOCATION_ID`)
    *   `DEMOGRAPHIC_KIND` (VARCHAR)
    *   `KIND` (VARCHAR)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)

*   **Table: `LOCATION_GRANT`**
    *   `LOCATION_GRANT_ID` (Primary Key)
    *   `LAND_PROPERTY_ID` (Foreign Key to `LAND_PROPERTY.LAND_PROPERTY_ID`)
    *   `OFFICIAL_SINCE` (DATE)
    *   `PROPERTY_DATA` (VARCHAR)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `CATEGORY`, `CREATED_DATE_TIME`, `DOC_REMARKS`, `DOC_STATUS`, `DOC_STATUS_DATE`, `LAST_MODIFIED_DATE_TIME`, `REVISION_NUMBER`, `SUBJECT`, `TITLE` (Inherited from `Document`)
    *   `END_DATE_TIME`, `SCOPE_INFORMATION`, `SIGN_DATE`, `START_DATE_TIME` (Inherited from `Agreement`)

*   **Table: `RIGHT_OF_WAY`**
    *   `RIGHT_OF_WAY_ID` (Primary Key)
    *   `LAND_PROPERTY_ID` (Foreign Key to `LAND_PROPERTY.LAND_PROPERTY_ID`)
    *   `PROPERTY_DATA` (VARCHAR)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `CATEGORY`, `CREATED_DATE_TIME`, `DOC_REMARKS`, `DOC_STATUS`, `DOC_STATUS_DATE`, `LAST_MODIFIED_DATE_TIME`, `REVISION_NUMBER`, `SUBJECT`, `TITLE` (Inherited from `Document`)
    *   `END_DATE_TIME`, `SCOPE_INFORMATION`, `SIGN_DATE`, `START_DATE_TIME` (Inherited from `Agreement`)

*   **Table: `ROUTE`**
    *   `ROUTE_ID` (Primary Key)
    *   `LOCATION_ID` (Foreign Key to `LOCATION.LOCATION_ID`)
    *   `CATEGORY` (VARCHAR)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)

*   **Table: `LOC_LOC_ROLE`**
    *   `LOC_LOC_ROLE_ID` (Primary Key)
    *   `FROM_LOCATION_ID` (Foreign Key to `LOCATION.LOCATION_ID`)
    *   `TO_LOCATION_ID` (Foreign Key to `LOCATION.LOCATION_ID`)
    *   `DIRECTIONS` (VARCHAR)
    *   `CATEGORY` (VARCHAR)
    *   `PRIVILEGE` (VARCHAR)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)

---

### 4. `VehicleAssetInformation.xsd`

This schema defines `VehicleAssetInformation` which contains `Vehicle`.

**Table: `VEHICLE_ASSET_INFORMATION`**
*   `VEHICLE_ASSET_INFORMATION_ID` (Primary Key)

**Table: `VEHICLE`**
*   `VEHICLE_ID` (Primary Key)
*   `VEHICLE_ASSET_INFORMATION_ID` (Foreign Key to `VEHICLE_ASSET_INFORMATION.VEHICLE_ASSET_INFORMATION_ID`)
*   `CATEGORY` (VARCHAR)
*   `CREW_REQUIREMENT` (VARCHAR)
*   `ETAG` (VARCHAR)
*   `FUEL_TYPE` (VARCHAR)
*   `ODOMETER_READING` (DECIMAL)
*   `ODOMETER_UNIT` (VARCHAR)
*   `ODOMETER_MULTIPLIER` (VARCHAR)
*   `USAGE_KIND` (VARCHAR)
*   `VEHICLE_MAKE` (VARCHAR)
*   `VEHICLE_MODEL` (VARCHAR)
*   `VEHICLE_TYPE` (VARCHAR)
*   `YEAR` (INTEGER)
*   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
*   `APPLICATION`, `CATEGORY_ASSET`, `CODE`, `CRITICAL`, `GUARANTEE_EXPIRY_DATE`, `INITIAL_CONDITION`, `INSTALLATION_DATE`, `LOT_NUMBER`, `MANUFACTURED_DATE`, `SERIAL_NUMBER`, `TEST_DATE`, `TEST_STATUS`, `TEST_TYPE`, `UTC_NUMBER` (Inherited from `Asset`)

**Related Tables for `VEHICLE`:**

*   **Table: `ASSET_MODEL`**
    *   `ASSET_MODEL_ID` (Primary Key)
    *   `VEHICLE_ID` (Foreign Key to `VEHICLE.VEHICLE_ID`)
    *   `CORPORATE_STANDARD_KIND` (VARCHAR)
    *   `MODEL_MAKE` (VARCHAR)
    *   `MODEL_NUMBER` (VARCHAR)
    *   `MODEL_VERSION` (VARCHAR)
    *   `USAGE_KIND` (VARCHAR)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)
    *   `CATEGORY`, `CREATED_DATE_TIME`, `DOC_REMARKS`, `DOC_STATUS`, `DOC_STATUS_DATE`, `LAST_MODIFIED_DATE_TIME`, `REVISION_NUMBER`, `SUBJECT`, `TITLE` (Inherited from `Document`)

*   **Table: `MEASUREMENT`**
    *   `MEASUREMENT_ID` (Primary Key)
    *   `VEHICLE_ID` (Foreign Key to `VEHICLE.VEHICLE_ID`)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)

*   **Table: `PERSON_ASSET_ROLE`**
    *   `PERSON_ASSET_ROLE_ID` (Primary Key)
    *   `VEHICLE_ID` (Foreign Key to `VEHICLE.VEHICLE_ID`)
    *   `ERP_PERSON_ID` (Foreign Key to `ERP_PERSON.ERP_PERSON_ID`)
    *   `CATEGORY` (VARCHAR)
    *   `PRIVILEGE` (VARCHAR)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)

*   **Table: `ASSET_PSR_ROLE`**
    *   `ASSET_PSR_ROLE_ID` (Primary Key)
    *   `VEHICLE_ID` (Foreign Key to `VEHICLE.VEHICLE_ID`)
    *   `POWER_SYSTEM_RESOURCE_ID` (Foreign Key to `POWER_SYSTEM_RESOURCE.POWER_SYSTEM_RESOURCE_ID`)
    *   `CATEGORY` (VARCHAR)
    *   `PRIVILEGE` (VARCHAR)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)

*   **Table: `POWER_SYSTEM_RESOURCE`**
    *   `POWER_SYSTEM_RESOURCE_ID` (Primary Key)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)

*   **Table: `ASSET_ASSET_ROLE`**
    *   `ASSET_ASSET_ROLE_ID` (Primary Key)
    *   `FROM_ASSET_ID` (Foreign Key to `VEHICLE.VEHICLE_ID`)
    *   `TO_ASSET_ID` (Foreign Key to `VEHICLE.VEHICLE_ID`)
    *   `CATEGORY` (VARCHAR)
    *   `PRIVILEGE` (VARCHAR)
    *   `MRID`, `ALIAS_NAME`, `DESCRIPTION`, `LOCAL_NAME`, `NAME`, `PATH_NAME` (Inherited from `IdentifiedObject`)

---

### 5. `message.xsd`

This schema defines generic message structures, which are more about the communication protocol than persistent data. However, some elements might be useful for logging or auditing.

**Table: `MESSAGE_HEADER`**
*   `MESSAGE_HEADER_ID` (Primary Key)
*   `VERB` (VARCHAR)
*   `NOUN` (VARCHAR)
*   `REVISION` (VARCHAR)
*   `CONTEXT` (VARCHAR)
*   `ORIGINAL_EVENT_DATE_TIME` (DATETIME)
*   `SOURCE` (VARCHAR)
*   `DESTINATION` (VARCHAR)
*   `ASYNC_REPLY_FLAG` (BOOLEAN)
*   `REPLY_ADDRESS` (VARCHAR)
*   `ACK_REQUIRED` (BOOLEAN)
*   `MESSAGE_ID` (VARCHAR)
*   `COMMENT` (VARCHAR)

**Table: `MESSAGE_PROPERTY`**
*   `MESSAGE_PROPERTY_ID` (Primary Key)
*   `MESSAGE_HEADER_ID` (Foreign Key to `MESSAGE_HEADER.MESSAGE_HEADER_ID`)
*   `NAME` (VARCHAR)
*   `VALUE` (VARCHAR)

**Table: `CORRELATION_ID`**
*   `CORRELATION_ID_PK` (Primary Key)
*   `MESSAGE_HEADER_ID` (Foreign Key to `MESSAGE_HEADER.MESSAGE_HEADER_ID`)
*   `NAME` (VARCHAR)
*   `VALUE` (VARCHAR)

**Table: `USER_INFO`**
*   `USER_INFO_ID` (Primary Key)
*   `MESSAGE_HEADER_ID` (Foreign Key to `MESSAGE_HEADER.MESSAGE_HEADER_ID`)
*   `USER_ID` (VARCHAR)
*   `ORGANIZATION` (VARCHAR)

**Table: `REPLAY_DETECTION`**
*   `REPLAY_DETECTION_ID` (Primary Key)
*   `NONCE` (VARCHAR)
*   `CREATED` (DATETIME)

**Table: `ERROR_LOG`**
*   `ERROR_LOG_ID` (Primary Key)
*   `REPLY_ID` (Foreign Key to `REPLY.REPLY_ID`)
*   `LEVEL` (VARCHAR)
*   `CODE` (VARCHAR)
*   `REASON` (VARCHAR)
*   `XPATH` (VARCHAR)
*   `DETAILS` (VARCHAR)
*   `CATEGORY` (VARCHAR)

**Table: `REPLY`**
*   `REPLY_ID` (Primary Key)
*   `REPLY_CODE` (VARCHAR)
*   `REPLY_DETAILS` (VARCHAR)
*   `ID` (VARCHAR)

---

### General Considerations:

*   **Primary Keys:** I've suggested `UUID` or auto-incrementing integers for primary keys. The choice depends on the specific database system and performance requirements.
*   **Foreign Keys:** Relationships are established using foreign keys.
*   **Data Types:** I've used generic SQL data types (VARCHAR, DATETIME, BOOLEAN, DECIMAL, FLOAT, INTEGER). These would need to be mapped to specific types for your chosen database (e.g., `NVARCHAR(255)`, `DATE`, `NUMBER(10,2)`).
*   **`IdentifiedObject` Inheritance:** Many complex types extend `IdentifiedObject`. I've denormalized these common attributes (`mRID`, `aliasName`, `description`, `localName`, `name`, `pathName`) into each table for simplicity, but they could also be in a separate `IDENTIFIED_OBJECT` table with a one-to-one relationship if strict normalization is preferred.
*   **`Document` Inheritance:** Similarly, attributes from `Document` are denormalized.
*   **`Status` Table:** The `Status` complex type appears frequently. It could be a separate table with a generic foreign key mechanism (e.g., `STATUS_FOR_TABLE`, `STATUS_FOR_ID`) or denormalized into each table that uses it. For simplicity, I've denormalized it into the tables where it's directly referenced.
*   **`Name` Table:** The `Names` element in `IdentifiedObject` is `maxOccurs="unbounded"`, suggesting a separate table for names associated with an `IdentifiedObject`.
