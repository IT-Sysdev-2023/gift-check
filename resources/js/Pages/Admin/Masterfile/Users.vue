<template>
    <a-card>
        <div>
            <a-button
                class="back-button"
                @click="backButton"
                style="border: 1px solid whitesmoke"
                ><RollbackOutlined /> Back</a-button
            >
        </div>

        <div style="margin-left: 82.5%">
            <a-button
                style="background-color: #1b76f8; color: white"
                @click="() => (open = true)"
            >
                <PlusOutlined />Add New User
            </a-button>
        </div>

        <div>
            <h2>Users Setup</h2>
        </div>

        <div style="margin-left: 70%">
            <a-input-search
                allow-clear
                v-model:value="searchTerm"
                size="medium"
                enter-button
                placeholder="Input search here!"
                style="width: 80%"
            />
        </div>

        <div style="margin-top: 10px">
            <a-table
                :dataSource="users.data"
                :columns="columns"
                :pagination="false"
                size="small"
            >
                <template v-slot:bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'action'">
                        <a-button
                            title="Update User"
                            @click="updateUser(record)"
                            class="me-2 me-sm-5"
                            style="color: white; background-color: green"
                        >
                            <FormOutlined />
                        </a-button>

                        <a-button
                            @click="resetPassword(record)"
                            title="Reset Password"
                            style="color: white; background-color: #1b76f8"
                        >
                            <UndoOutlined />
                        </a-button>

                        <!-- <a-button @click="DangerButton(record)" style="color:white; background-color: #C7253E;" title="danger" size="small">
                            <WarningOutlined />
                        </a-button> -->
                    </template>
                </template>
            </a-table>
            <pagination
                v-model:value="pagination"
                :datarecords="users"
                class="mt-5"
            />
        </div>
    </a-card>

    <!-- this is for updating user -->
    <a-modal v-model:open="modalforUpdateUser" @ok="updateStoreStaff">
        <span style="color: #0286df; font-size: 17px">
            <UserAddOutlined style="margin-right: 8px" />
            Update User
        </span>
        <!-- {{ dataForUpdateUser }} -->

        <a-form-item
            for="username"
            :validate-status="dataForUpdateUser.errors?.username ? 'error' : ''"
            :help="dataForUpdateUser.errors?.username"
            style="margin-top: 10px"
        >
            Username:

            <a-input
                allow-clear
                v-model:value="dataForUpdateUser.username"
                placeholder="Username"
            />
        </a-form-item>

        <a-form-item
            for="firstname"
            :validate-status="
                dataForUpdateUser.errors?.firstname ? 'error' : ''
            "
            :help="dataForUpdateUser.errors?.firstname"
        >
            firstname:
            <a-input
                allow-clear
                v-model:value="dataForUpdateUser.firstname"
                placeholder="Firstname"
            />
        </a-form-item>

        <a-form-item
            for="lastname"
            :validate-status="dataForUpdateUser.errors?.lastname ? 'error' : ''"
            :help="dataForUpdateUser.errors?.lastname"
        >
            Lastname:
            <a-input
                allow-clear
                v-model:value="dataForUpdateUser.lastname"
                placeholder="Lastname"
            />
        </a-form-item>

        <a-form-item
            for="emp_id"
            :validate-status="dataForUpdateUser.errors?.emp_id ? 'error' : ''"
            :help="dataForUpdateUser.errors?.emp_id"
        >
            Employee ID:
            <a-input
                allow-clear
                v-model:value="dataForUpdateUser.emp_id"
                placeholder="Employee ID"
            />
        </a-form-item>

        <!-- this is user type part -->
        <a-form-item
            for="usertype"
            :validate-status="dataForUpdateUser.errors?.usertype ? 'error' : ''"
            :help="dataForUpdateUser.errors?.usertype"
        >
            User Type:
            <a-select
                v-model:value="dataForUpdateUser.usertype"
                style="width: 472px"
            >
                <a-select-option
                    v-for="item in access_page"
                    :key="item.access_no"
                    :value="item.access_no"
                >
                    {{ item.employee_type }}
                </a-select-option>
            </a-select>
        </a-form-item>

        <!-- <a-input v-model:value="dataForUpdateUser.store_assigned" :value="''" type="hidden">

        </a-input> -->
        <!-- {{ dataForUpdateUser.usertype }} -->

        <!-- this is store assigned part -->
        <a-form-item
            for="store_assigned"
            :validate-status="
                dataForUpdateUser.errors?.store_assigned ? 'error' : ''
            "
            :help="dataForUpdateUser.errors?.store_assigned"
            v-if="
                (dataForUpdateUser.usertype === 7 ||
                    dataForUpdateUser.usertype === 'retailstore' ||
                    dataForUpdateUser.usertype === 14 ||
                    dataForUpdateUser.usertype === 'store_accounting' ||
                    dataForUpdateUser.it_type === '2' ||
                    dataForUpdateUser.it_type === 'store_it') &&
                dataForUpdateUser.usertype !== 1 &&
                dataForUpdateUser.usertype !== 2 &&
                dataForUpdateUser.usertype !== 3 &&
                dataForUpdateUser.usertype !== 4 &&
                dataForUpdateUser.usertype !== 5 &&
                dataForUpdateUser.usertype !== 6 &&
                dataForUpdateUser.usertype !== 8 &&
                dataForUpdateUser.usertype !== 9 &&
                dataForUpdateUser.usertype !== 10 &&
                dataForUpdateUser.usertype !== 11 &&
                dataForUpdateUser.usertype !== 13
            "
        >
            Store Assigned:
            <a-select
                v-model:value="dataForUpdateUser.store_assigned"
                style="width: 472px"
                placeholder="Store Assigned"
            >
                <a-select-option
                    v-for="item in store"
                    :key="item.store_id"
                    :value="item.store_id"
                >
                    {{ item.store_name }}
                </a-select-option>
            </a-select>
        </a-form-item>
        <!-- this user role part -->
        <a-form-item
            for="user_role"
            :validate-status="
                dataForUpdateUser.errors?.user_role ? 'error' : ''
            "
            :help="dataForUpdateUser.errors?.user_role"
            v-if="
                (dataForUpdateUser.it_type !== '2' &&
                    dataForUpdateUser.usertype !== 'it_personnel') ||
                dataForUpdateUser.usertype === 1 ||
                dataForUpdateUser.usertype === 'administrator' ||
                dataForUpdateUser.usertype === 2 ||
                dataForUpdateUser.usertype === 'treasurydept' ||
                dataForUpdateUser.usertype === 3 ||
                dataForUpdateUser.usertype === 'finance' ||
                dataForUpdateUser.usertype === 4 ||
                dataForUpdateUser.usertype === 'custodian' ||
                dataForUpdateUser.usertype === 5 ||
                dataForUpdateUser.usertype === 'generalmanager' ||
                dataForUpdateUser.usertype === 6 ||
                dataForUpdateUser.usertype === 'marketing' ||
                dataForUpdateUser.usertype === 7 ||
                dataForUpdateUser.usertype === 'retailstore' ||
                dataForUpdateUser.usertype === 8 ||
                dataForUpdateUser.usertype === 'retailgroup' ||
                dataForUpdateUser.usertype === 9 ||
                dataForUpdateUser.usertype === 'accounting' ||
                dataForUpdateUser.usertype === 10 ||
                dataForUpdateUser.usertype === 'internal_audit' ||
                dataForUpdateUser.usertype === 11 ||
                dataForUpdateUser.usertype === 'finance_office' ||
                dataForUpdateUser.usertype === 13 ||
                dataForUpdateUser.usertype === 'cfs' ||
                dataForUpdateUser.usertype === 14 ||
                dataForUpdateUser.usertype === 'store_accounting' ||
                dataForUpdateUser.it_type === '1'
            "
        >
            User Role:
            <a-select
                id="user_role"
                v-model:value="dataForUpdateUser.user_role"
                style="width: 472px"
                placeholder="User Role"
            >
                <a-select-option value="1">Department User</a-select-option>
                <a-select-option value="2">Department Manager</a-select-option>
            </a-select>
        </a-form-item>

        <!-- this is retail group part -->
        <a-form-item
            for="retail_group"
            :validate-status="
                dataForUpdateUser.errors?.retail_group ? 'error' : ''
            "
            :help="dataForUpdateUser.errors?.retail_group"
            v-if="
                !(
                    dataForUpdateUser.usertype === 1 ||
                    dataForUpdateUser.usertype === 2 ||
                    dataForUpdateUser.usertype === 3 ||
                    dataForUpdateUser.usertype === 4 ||
                    dataForUpdateUser.usertype === 5 ||
                    dataForUpdateUser.usertype === 6 ||
                    dataForUpdateUser.usertype === 7 ||
                    dataForUpdateUser.usertype === 9 ||
                    dataForUpdateUser.usertype === 10 ||
                    dataForUpdateUser.usertype === 11 ||
                    dataForUpdateUser.usertype === 12 ||
                    dataForUpdateUser.usertype === 13 ||
                    dataForUpdateUser.usertype === 14
                ) &&
                (dataForUpdateUser.usertype === 'retailgroup' ||
                    dataForUpdateUser.usertype === 8)
            "
        >
            Retail Group:
            <a-select
                id="retail_group"
                v-model:value="dataForUpdateUser.retail_group"
                style="width: 472px"
                placeholder="Retail Group"
            >
                <a-select-option value="1">Group 1</a-select-option>
                <a-select-option value="2">Group 2</a-select-option>
            </a-select>
        </a-form-item>

        <!-- this is IT Type part -->

        <a-form-item
            for="it_type"
            :validate-status="dataForUpdateUser.errors?.it_type ? 'error' : ''"
            :help="dataForUpdateUser.errors?.it_type"
            v-if="
                ![1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 13, 14].includes(
                    dataForUpdateUser.usertype,
                ) &&
                (dataForUpdateUser.usertype === 'it_personnel' ||
                    dataForUpdateUser.usertype === 12)
            "
        >
            IT Type:
            <a-select
                id="it_type"
                v-model:value="dataForUpdateUser.it_type"
                style="width: 472px"
                placeholder="IT Type"
            >
                <a-select-option value="1">Corporate IT</a-select-option>
                <a-select-option value="2">Store IT</a-select-option>
            </a-select>
        </a-form-item>
        <!-- {{ dataForUpdateUser.it_type }} -->

        <!-- this is user status part -->
        <a-form-item
            for="user_status"
            :validate-status="
                dataForUpdateUser.errors?.user_status ? 'error' : ''
            "
            :help="dataForUpdateUser.errors?.user_status"
        >
            Status:
            <a-select
                id="user_status"
                v-model:value="dataForUpdateUser.user_status"
                style="width: 472px"
                placeholder="Set status"
            >
                <a-select-option value="active">ACTIVE</a-select-option>
                <a-select-option value="inactive">INACTIVE</a-select-option>
            </a-select>
        </a-form-item>
    </a-modal>

    <!-- this is for resetting password -->
    <a-modal v-model:open="modalforresetPassword" @ok="submitResetPassword">
        <span style="color: #c7253e; font-size: 17px">
            Confirmation
            <QuestionOutlined />
        </span>

        <a-form-item for="password" style="font-style: initial">
            Reset
            <span
                style="
                    color: #c7253e;
                    margin-left: 5px;
                    margin-right: 5px;
                    text-decoration: underline;
                "
            >
                {{ dataforResetPassword.full_name }}
            </span>
            password to default?
        </a-form-item>
    </a-modal>

    <!-- this is for adding user -->
    <a-modal v-model:open="open" @ok="handleOk">
        <span style="color: #0286df; font-size: 17px">
            <UserAddOutlined style="margin-right: 8px" />
            Add New User
        </span>

        <a-form-item
            for="username"
            :validate-status="form.errors.username ? 'error' : ''"
            :help="form.errors.username"
            style="margin-top: 10px"
        >
            Username:
            <a-input
                allow-clear
                v-model:value="form.username"
                placeholder="Username"
            />
        </a-form-item>

        <a-form-item
            for="firstname"
            :validate-status="form.errors.firstname ? 'error' : ''"
            :help="form.errors.firstname"
        >
            Firstname:
            <a-input
                allow-clear
                v-model:value="form.firstname"
                placeholder="Firstname"
            />
        </a-form-item>

        <a-form-item
            for="lastname"
            :validate-status="form.errors.lastname ? 'error' : ''"
            :help="form.errors.lastname"
        >
            Lastname:
            <a-input
                allow-clear
                v-model:value="form.lastname"
                placeholder="Lastname"
            />
        </a-form-item>

        <a-form-item
            for="employee_id"
            :validate-status="form.errors.emp_id ? 'error' : ''"
            :help="form.errors.emp_id"
        >
            Employee_id:
            <a-input
                allow-clear
                v-model:value="form.emp_id"
                placeholder="Employee_id"
            />
        </a-form-item>

        <a-form-item
            for="usertype"
            :validate-status="form.errors.employee_type ? 'error' : ''"
            :help="form.errors.employee_type"
        >
            User Type:
            <a-select
                v-model:value="form.employee_type"
                style="width: 472px"
                placeholder="User Type"
            >
                <a-select-option
                    v-for="item in access_page"
                    :key="item.employee_type"
                    :value="item.employee_type"
                    >{{ item.employee_type }}</a-select-option
                >
            </a-select>
        </a-form-item>
        <!-- {{ form.employee_type }} -->

        <a-form-item
            for="user_role"
            :validate-status="form.errors.user_role ? 'error' : ''"
            :help="form.errors.user_role"
            v-if="
                form.employee_type === 'administrator' ||
                form.employee_type === 'treasurydept' ||
                form.employee_type === 'custodian' ||
                form.employee_type === 'generalmanager' ||
                form.employee_type === 'marketing' ||
                form.employee_type === 'retailstore' ||
                form.employee_type === 'retailgroup' ||
                form.employee_type === 'accounting' ||
                form.employee_type === 'internal_audit' ||
                form.employee_type === 'finance_office' ||
                form.employee_type === 'cfs' ||
                form.employee_type === 'store_accounting' ||
                form.employee_type === 'finance' ||
                form.it_type === '1'
            "
        >
            User Role:
            <a-select
                id=" user_role"
                v-model:value="form.user_role"
                style="width: 472px"
                placeholder="Select User Role"
            >
                <a-select-option value="1">Department User</a-select-option>
                <a-select-option value="2">Department Manager</a-select-option>
            </a-select>
        </a-form-item>

        <a-form-item
            for="store_assigned"
            :validate-status="form.errors.store_name ? 'error' : ''"
            :help="form.errors.store_name"
            v-if="
                form.employee_type === 'retailstore' ||
                form.employee_type === 'store_accounting' ||
                (form.it_type === '2' && form.employee_type !== 'cfs')
            "
        >
            Store Assigned:
            <a-select
                v-model:value="form.store_name"
                style="width: 472px"
                placeholder="Select Store"
            >
                <a-select-option
                    v-for="item in store"
                    :key="item.store_name"
                    :value="item.store_name"
                >
                    {{ item.store_name }}</a-select-option
                >
            </a-select>
        </a-form-item>

        <a-form-item
            for="retail_group"
            :validate-status="form.errors.retail_group ? 'error' : ''"
            :help="form.errors.retail_group"
            v-if="form.employee_type === 'retailgroup'"
        >
            Retail Group:
            <a-select
                id=" retail_group"
                v-model:value="form.retail_group"
                style="width: 472px"
                placeholder="Select User Role"
            >
                <a-select-option value="1">Group 1</a-select-option>
                <a-select-option value="2">Group 2</a-select-option>
            </a-select>
        </a-form-item>

        <a-form-item
            for="IT Type"
            :validate-status="form.errors.it_type ? 'error' : ''"
            :help="form.errors.it_type"
            v-if="form.employee_type === 'it_personnel'"
        >
            IT Type:
            <a-select
                id="it_type"
                v-model:value="form.it_type"
                style="width: 472px"
                placeholder="Select IT Type"
            >
                <a-select-option value="1">Corporate IT</a-select-option>
                <a-select-option value="2">Store_IT</a-select-option>
            </a-select>
        </a-form-item>
    </a-modal>
    <!-- <a-modal v-model:open="danger">
        <span>KUPAL KA TALAGA</span>
    </a-modal> -->

    <!-- {{ users }} -->
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { UserAddOutlined } from "@ant-design/icons-vue";
import { notification } from "ant-design-vue";
export default {
    layout: AuthenticatedLayout,
    components: { UserAddOutlined },
    name: "UserView",
    props: {
        access_page: Object,
        users: Object,
        storestaff: Object,
        store: Object,
        search: String,
        value: String,
        filterstore: String,
    },
    data() {
        return {
            // danger: false,

            dataForFilterStore: {
                filter_store: this.filterstore,
            },
            dataForSelectEntries: {
                show_entries: this.value,
            },
            searchTerm: this.search,

            form: this.$inertia.form({
                username: "",
                firstname: "",
                lastname: "",
                emp_id: "",
                user_role: "",
                store_name: "",
                employee_type: "",
                retail_group: "",
                it_type: "",
            }),
            dataforResetPassword: this.$inertia.form({
                user_id: "",
                full_name: "",
            }),

            dataForUpdateUser: this.$inertia.form({
                username: "",
                firstname: "",
                lastname: "",
                emp_id: "",
                usertype: "",
                user_status: "",
                employee_type: "",
                retail_group: "",
                store_assigned: "",
                it_type: "",
                user_role: "",
                store_name: "",
                errors: {},
            }),
            modalforDelete: false,
            modalforresetPassword: false,
            modalforUpdateUser: false,
            open: false,
            columns: [
                {
                    title: "Username",
                    dataIndex: "username",
                    sorter: (a, b) => {
                        return a.username
                            .charAt(0)
                            .toUpperCase()
                            .localeCompare(b.username.charAt(0).toUpperCase());
                    },
                },
                {
                    title: "Employee ID",

                    dataIndex: "emp_id",
                },
                {
                    title: "First Name",
                    dataIndex: "firstname",
                    sorter: (a, b) => {
                        return a.firstname
                            .charAt(0)
                            .toUpperCase()
                            .localeCompare(b.firstname.charAt(0).toUpperCase());
                    },
                },
                {
                    title: "Last Name",
                    dataIndex: "lastname",
                    sorter: (a, b) => {
                        return a.lastname
                            .charAt(0)
                            .toUpperCase()
                            .localeCompare(b.lastname.charAt(0).toUpperCase());
                    },
                },
                {
                    title: "User Type",
                    dataIndex: "title",
                    filters: [
                        {
                            text: "Administrator",
                            value: "Administrator",
                        },
                        {
                            text: "Corporate Treasury",
                            value: "Corporate Treasury",
                        },
                        {
                            text: "Corporate Finance",
                            value: "Corporate Finance",
                        },
                        {
                            text: "Custodian/Corp-Fad",
                            value: "Corporate FAD",
                        },
                        {
                            text: "General Manager",
                            value: "General Manager",
                        },
                        {
                            text: "Corporate Marketing",
                            value: "Corporate Marketing",
                        },
                        {
                            text: "Retail Store",
                            value: "Retail Store",
                        },
                        {
                            text: "Retail Group",
                            value: "Retail Group",
                        },
                        {
                            text: "Corporate Accounting",
                            value: "Corporate Accounting",
                        },
                        {
                            text: "Internal Audit",
                            value: "Internal Audit ",
                        },
                        {
                            text: "FInance Office",
                            value: "Finance Officer",
                        },
                        {
                            text: "IT Personnel",
                            value: "IT Personnel",
                        },
                        {
                            text: "CFS",
                            value: "CFS",
                        },
                        {
                            text: "Store Accounting",
                            value: "Store Accounting",
                        },
                    ],
                    onFilter: (value, record) => record.title === value,
                    width: "10%",
                },
                {
                    title: "BU Assigned",
                    dataIndex: "store_name",
                    filters: [
                        {
                            text: "Alturas Mall",
                            value: "Alturas Mall",
                        },
                        {
                            text: "Alturas Talibon",
                            value: "Alturas Talibon",
                        },
                        {
                            text: "Island City Mall",
                            value: "Island City Mall",
                        },
                        {
                            text: "Plaza Marcela",
                            value: "Plaza Marcela",
                        },
                        {
                            text: "Alturas Tubigon",
                            value: "Alturas Tubigon",
                        },
                        {
                            text: "Colonade Colon",
                            value: "Colonade Colon",
                        },
                        {
                            text: "Colonade Mandaue",
                            value: "Colonade Mandaue",
                        },
                        {
                            text: "Alta Citta",
                            value: "Alta Citta",
                        },
                        {
                            text: "Farmers Market",
                            value: "Farmers Market",
                        },
                        {
                            text: "Ubay Distribution Center",
                            value: "Ubay Distribution Center ",
                        },
                        {
                            text: "Screenville",
                            value: "Screenville",
                        },
                        {
                            text: "Asc Tech",
                            value: "Asc Tech",
                        },
                    ],
                    onFilter: (value, record) => record.store_name === value,
                    width: "10%",
                },
                {
                    title: "Status",
                    dataIndex: "user_status",
                    filters: [
                        {
                            text: "ACTIVE",
                            value: "active",
                        },
                        {
                            text: "INACTIVE",
                            value: "inactive",
                        },
                    ],
                    onFilter: (value, record) => record.user_status === value,
                    width: "8%",
                },
                {
                    title: "Date User Added",
                    dataIndex: "date_created",
                },
                {
                    title: "Action",
                    dataIndex: "action",
                },
            ],
        };
    },
    watch: {
        searchTerm(newVal) {
            console.log(newVal);
            this.$inertia.get(
                route("admin.masterfile.users"),
                {
                    data: newVal,
                },
                {
                    preserveState: true,
                },
            );
        },
    },
    methods: {
        updateUser(data) {
            this.modalforUpdateUser = true;
            this.dataForUpdateUser = { ...data };
            this.dataForUpdateUser.username = data.username;
            this.dataForUpdateUser.store_assigned = data.store_name;
            this.dataForUpdateUser.usertype = data.employee_type;
            this.dataForUpdateUser.user_role = data.user_role;
            this.dataForUpdateUser.retail_group = data.retail_group;
            this.dataForUpdateUser.it_type = data.it_type;
            this.dataForUpdateUser.retailstore = data.retailstore;
        },
        updateStoreStaff() {
            this.dataForUpdateUser.errors = {};
            if (!this.dataForUpdateUser.username) {
                this.dataForUpdateUser.errors.username =
                    "The username field is required";
            }
            if (!this.dataForUpdateUser.firstname) {
                this.dataForUpdateUser.errors.firstname =
                    "The firstname field is required";
            }
            if (!this.dataForUpdateUser.lastname) {
                this.dataForUpdateUser.errors.lastname =
                    "The lastname field is required";
            }
            if (!this.dataForUpdateUser.emp_id) {
                this.dataForUpdateUser.errors.emp_id =
                    "The employee ID field is required";
            }
            if (!this.dataForUpdateUser.usertype) {
                this.dataForUpdateUser.errors.usertype =
                    "The usertype field is required";
            }
            if (!this.dataForUpdateUser.employee_type) {
                this.dataForUpdateUser.errors.employee_type =
                    "The employee type field is required";
            }
            if (!this.dataForUpdateUser.retail_group) {
                this.dataForUpdateUser.errors.retail_group =
                    "The retail group field is required";
            }
            if (!this.dataForUpdateUser.store_assigned) {
                this.dataForUpdateUser.errors.store_assigned =
                    "The store assigned field is required";
            }
            if (!this.dataForUpdateUser.it_type) {
                this.dataForUpdateUser.errors.it_type =
                    "The IT type field is required";
            }
            if (!this.dataForUpdateUser.user_role) {
                this.dataForUpdateUser.errors.user_role =
                    "The user role field is required";
            }
            if (!this.dataForUpdateUser.store_name) {
                this.dataForUpdateUser.errors.store_name =
                    "The store name field is required";
            }
            this.$inertia.post(
                route("admin.masterfile.updateUser"),
                {
                    ...this.dataForUpdateUser,
                },
                {
                    onSuccess: ({ props }) => {
                        if (props.flash.success) {
                            notification.success({
                                message: props.flash.success,
                                description: `${this.dataForUpdateUser.username} data updated successfully!`,
                            });
                            this.modalforUpdateUser = false;
                        } else if (props.flash.error) {
                            notification.warning({
                                message: props.flash.error,
                                description: `${this.dataForUpdateUser.username} data has no changes happen, update first before submitting! `,
                            });
                        }
                    },
                },
            );
        },
        handleOk() {
            // alert(1)
            this.form.get(route("admin.masterfile.user.saveUser"), {
                preserveState: true,
                onSuccess: ({ props }) => {
                    if (props.flash.success) {
                        notification.success({
                            message: props.flash.success,
                            description: "Users added successfully!",
                        });
                        this.open = false;
                        this.form.username = "";
                        this.form.firstname = "";
                        this.form.lastname = "";
                        this.form.emp_id = "";
                        this.form.user_role = "";
                        this.form.store_name = "";
                        this.form.employee_type = "";
                        this.form.retail_group = "";
                        this.form.it_type = "";
                    } else if (props.flash.error) {
                        notification.warning({
                            message: props.flash.error,
                            description:
                                "The username already exists, please try again!",
                        });
                    }
                },
            });
        },
        resetPassword(rec) {
            this.dataforResetPassword.full_name = rec.full_name;
            this.dataforResetPassword.username = rec.username;
            this.dataforResetPassword.user_id = rec.user_id;
            this.modalforresetPassword = true;
        },
        submitResetPassword() {
            this.$inertia.get(
                route("admin.masterfile.usersResetPassword"),
                {
                    // ...this.dataforResetPassword,
                    user_id: this.dataforResetPassword.user_id,
                },
                {
                    onSuccess: ({ props }) => {
                        if (props.flash.success) {
                            notification.success({
                                message: props.flash.success,
                                description: `${this.dataforResetPassword.username} password is successfully reset!`,
                            });
                            this.modalforresetPassword = false;
                        } else if (props.flash.error) {
                            notification.warning({
                                message: props.flash.error,
                                description: `${this.dataforResetPassword.username} password already reset to default!`,
                            });
                        }
                    },
                    onError: (e) => {
                        this.dataforResetPassword.errors = e;
                    },
                },
            );
        },
        changeShowEntries(value) {
            console.log(value);
            this.$inertia.get(
                route("admin.masterfile.users"),
                {
                    value: value,
                },
                {
                    preserveState: true,
                    preserveScroll: true,
                },
            );
        },
        backButton() {
            this.$inertia.get(route("admin.dashboard"));
        },
    },
};
</script>
<style>
.user-button {
    text-align: right;
}

.add-user-button {
    background-color: #0286df;
    color: white;
    margin-right: 6%;
}

.search-button {
    text-align: right;
    font-weight: bold;
}

.search-input {
    border: 1px solid #0286df;
    width: 20%;
    margin-right: 8%;
    min-width: 120px;
    margin-top: 1%;
}
.back-button{
    font-weight: bold;
    font-family: 'Poppins', sans-serif;
}
</style>
