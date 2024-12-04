<template>
    <a-card>
        <a-card title="STORE STAFF SETUP"> </a-card>
        <div style="margin-left: 82.5%;">
            <a-button style="background-color: #1e90ff; color:white" @click="() => open = true">
                <PlusOutlined /> Add New User
            </a-button>
        </div>

        <div style="margin-left: 70%; margin-top: 10px;">
            <a-input-search allow-clear placeholder="Input search here!" v-model:value="searchTerm" size="medium"
                style="width: 80%;" />
        </div>
        <div style="margin-top: 10px;">
            <a-table :columns="columns" :data-source="data.data" :pagination="false" size="small">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'action'">
                        <a-button @click="updateStoreStaffSetup(record)" title="Update" class="me-2 me-sm-5"
                            style="color: white; background-color: green;" >
                            <FormOutlined />
                        </a-button>
                        <a-button @click="changePassword(record)" title="Change Password"
                            style="color: white; background-color: #1e90ff;" >
                            <UndoOutlined />
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="data" class="mt-5" />
        </div>
    </a-card>


    <!-- this is for update password part -->

    <a-modal v-model:open="modalforchangePassword" @ok="updatePassword">
        <span style="color: #C7253E; font-size: 17px;">
            Confirmation
            <QuestionOutlined />
        </span>

        <a-form-item for="password" style="margin-top: 15px; font-style: initial;">
            Reset
            <span style="color: #C7253E; margin-left: 5px; margin-right: 5px; text-decoration: underline; ">
                {{ dataforChangePassword.ss_username }}
            </span>
            password?
        </a-form-item>
    </a-modal>

    <!-- this is for update part -->
    <a-modal v-model:open="openmodalforupdate" @ok="updateStoreStaff">
        <span style="color:  #0286df; font-size: 17px;">
            <UserAddOutlined style=" margin-right: 8px;" />
            Update User
        </span>

        <a-form-item for="ss_username" :validate-status="dataForUpdate.errors?.ss_username ? 'error' : ''"
            :help="dataForUpdate.errors?.ss_username" style="margin-top: 10px;">
            Username:
            <a-input allow-clear v-model:value="dataForUpdate.ss_username" placeholder="Username" />
        </a-form-item>

        <a-form-item for="ss_firstname" :validate-status="dataForUpdate.errors?.ss_firstname ? 'error' : ''"
            :help="dataForUpdate.errors?.ss_firstname">
            firstname:
            <a-input allow-clear v-model:value="dataForUpdate.ss_firstname" placeholder="Firstname" />
        </a-form-item>

        <a-form-item for="ss_lastname" :validate-status="dataForUpdate.errors?.ss_lastname ? 'error' : ''"
            :help="dataForUpdate.errors?.ss_lastname">
            Lastname:
            <a-input allow-clear v-model:value="dataForUpdate.ss_lastname" placeholder="Lastname" />
        </a-form-item>

        <a-form-item for="ss_idnumber" :validate-status="dataForUpdate.errors?.ss_idnumber ? 'error' : ''"
            :help="dataForUpdate.errors?.ss_idnumber">
            Employee ID:
            <a-input allow-clear v-model:value="dataForUpdate.ss_idnumber" placeholder="Employee ID" />
        </a-form-item>

        <a-form-item for="ss_store" :validate-status="dataForUpdate.errors?.ss_store ? 'error' : ''"
            :help="dataForUpdate.errors?.ss_store">
            Store Assigned:
            <a-select v-model:value="dataForUpdate.ss_store" style="width: 472px;" placeholder="Select Store">
                <a-select-option v-for="item in store" :key="item.store_id" :value="item.store_id">
                    {{ item.store_name }}</a-select-option>
            </a-select>

        </a-form-item>
        <a-form-item for="ss_usertype" :validate-status="dataForUpdate.errors?.ss_usertype ? 'error' : ''"
            :help="dataForUpdate.errors?.ss_usertype">
            User Type:
            <a-select id="usertype" v-model:value="dataForUpdate.ss_usertype" style="width: 472px;"
                placeholder="Select User Type">
                <a-select-option value="cashier">CASHIER</a-select-option>
                <a-select-option value="manager">MANAGER</a-select-option>
            </a-select>
            <!-- <a-input v-model:value="dataForUpdate.ss_usertype" placeholder="User Type" /> -->
        </a-form-item>
    </a-modal>
    <!-- this is for adding user part -->
    <a-modal v-model:open="open" @ok="handleOk">
        <span style="color: #0286df; font-size: 17px;">
            <UserAddOutlined style="margin-right: 8px;" />
            Add New User
        </span>

        <a-form-item for="username" :validate-status="form.errors.username ? 'error' : ''" :help="form.errors.username"
            style="margin-top: 10px;">
            Username:
            <a-input allow-clear v-model:value="form.username" placeholder="Username" />
        </a-form-item>

        <a-form-item for="firstname" :validate-status="form.errors.firstname ? 'error' : ''"
            :help="form.errors.firstname">
            Firstname:
            <a-input allow-clear v-model:value="form.firstname" placeholder="Firstname" />
        </a-form-item>

        <a-form-item for="lastname" :validate-status="form.errors.lastname ? 'error' : ''" :help="form.errors.lastname">
            Lastname:
            <a-input allow-clear v-model:value="form.lastname" placeholder="Lastname" />
        </a-form-item>

        <a-form-item for="employee_id" :validate-status="form.errors.employee_id ? 'error' : ''"
            :help="form.errors.employee_id">
            Employee ID:
            <a-input allow-clear v-model:value="form.employee_id" placeholder="Employee ID" />
        </a-form-item>
        <!-- {{ form.errors.idnumber }} -->
        <a-form-item for="password" :validate-status="form.errors.password ? 'error' : ''" :help="form.errors.password">
            Password:
            <a-input type="password" allow-clear v-model:value="form.password" placeholder="Password" />
        </a-form-item>

        <a-form-item for="store_name" :validate-status="form.errors.store_id ? 'error' : ''"
            :help="form.errors.store_id">Store
            Assigned:
            <a-select v-model:value="form.store_id" style="width: 472px;" placeholder="Select Store">
                <a-select-option v-for="item in store" :key="item.store_id" :value="item.store_id">
                    {{ item.store_name }}</a-select-option>
            </a-select>
        </a-form-item>
        <a-form-item for="user_type" :validate-status="form.errors.usertype ? 'error' : ''"
            :help="form.errors.usertype">
            User Type:
            <a-select id="usertype" v-model:value="form.usertype" style="width: 472px;" placeholder="Select User Type">
                <a-select-option value="cashier">CASHIER</a-select-option>
                <a-select-option value="manager">MANAGER</a-select-option>
            </a-select>
        </a-form-item>
    </a-modal>
    {{ data.store_name }}
</template>
<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { DeleteOutlined, ReloadOutlined, PlusSquareOutlined, UserOutlined, EditOutlined, UnlockTwoTone, CloseCircleTwoTone, AppstoreTwoTone, UndoOutlined, PlusOutlined, EditFilled, EditTwoTone, FormOutlined } from '@ant-design/icons-vue';
import { notification } from 'ant-design-vue';


export default {
    layout: AuthenticatedLayout,
    components: { DeleteOutlined, ReloadOutlined, PlusSquareOutlined, EditOutlined, UserOutlined, UnlockTwoTone, CloseCircleTwoTone, AppstoreTwoTone, UndoOutlined, PlusOutlined, EditFilled, EditTwoTone, FormOutlined },
    props: {
        password: Object,
        store: Object,
        data: Object,
        StoreStaff: Object,
        search: String,
        value: String

    },

    data() {
        return {
            dataForSelectEntries: {
                select_entries: this.value
            },
            searchTerm: this.search,

            form: this.$inertia.form({
                username: '',
                firstname: '',
                lastname: '',
                employee_id: '',
                password: '',
                store_id: '',
                usertype: ''
            }),

            form: this.$inertia.form({
                username: '',
                firstname: '',
                lastname: '',
                employee_id: '',
                password: '',
                store_id: '',
                usertype: ''
            }),

            dataForUpdate: this.$inertia.form({
                ss_username: '',
                ss_firstname: '',
                ss_lastname: '',
                ss_idnumber: '',
                ss_store: '',
                ss_usertype: '',
                errors: {}
            }),
            dataforChangePassword: this.$inertia.form({
                ss_password: '',
                ss_id: ''

            }),
            modalforchangePassword: false,
            openmodalforupdate: false,
            open: false,
            columns: [{
                title: 'Username',
                dataIndex: 'ss_username',
                sorter: (a, b) => {
                    return a.ss_username.charAt(0).toUpperCase().localeCompare(b.ss_username.charAt(0).toUpperCase());
                }
            },
            {
                title: 'Firstname',
                dataIndex: 'ss_firstname',
                sorter: (a, b) => {
                    return a.ss_firstname.charAt(0).toUpperCase().localeCompare(b.ss_firstname.charAt(0).toUpperCase());
                }
            },
            {
                title: 'Lastname',
                dataIndex: 'ss_lastname',
                sorter: (a, b) => {
                    return a.ss_lastname.charAt(0).toUpperCase().localeCompare(b.ss_lastname.charAt(0).toUpperCase());
                }
            },
            {
                title: 'Store Assigned',
                dataIndex: 'store_name',
                filters: [
                    {
                        text: 'Alturas Mall',
                        value: 'Alturas Mall',
                    },
                    {
                        text: 'Alturas Talibon',
                        value: 'Alturas Talibon',
                    },
                    {
                        text: 'Island City Mall',
                        value: 'Island City Mall',
                    },
                    {
                        text: 'Plaza Marcela',
                        value: 'Plaza Marcela',
                    },
                    {
                        text: 'Alturas Tubigon',
                        value: 'Alturas Tubigon',
                    },
                    {
                        text: 'Colonade Colon',
                        value: 'Colonade Colon',
                    },
                    {
                        text: 'Colonade Mandaue',
                        value: 'Colonade Mandaue',
                    },
                    {
                        text: 'Alta Citta',
                        value: 'Alta Citta',
                    },
                    {
                        text: 'Farmers Market',
                        value: 'Farmers Market',
                    },
                    {
                        text: 'Ubay Distribution Center',
                        value: 'Ubay Distribution Center',
                    },
                    {
                        text: 'Screenville',
                        value: 'Screenville',
                    },
                    {
                        text: 'Asc Tech',
                        value: 'Asc Tech',
                    }
                ],
                onFilter: (value, record) => record.store_name === value,
            },

            {
                title: 'Emp ID No.',
                dataIndex: 'ss_idnumber'
            },
            {
                title: 'User Type',
                dataIndex: 'ss_usertype',
                filters: [
                    {
                        text: 'CASHIER',
                        value: 'cashier'
                    },
                    {
                        text: 'MANAGER',
                        value: 'manager'
                    },
                ],
                onFilter: (value, record) => record.ss_usertype === value
            },
            {
                title: 'Date Created',
                dataIndex: 'ss_date_created'
            },
            {
                title: 'Status',
                dataIndex: 'ss_status'
            },
            {
                title: 'Action',
                dataIndex: 'action'

            }
            ]
        }
    },
    watch: {
        searchTerm(newVal) {

            console.log(newVal);
            this.$inertia.get(route('admin.masterfile.store.staff'), {
                data: newVal
            }, {
                preserveState: true
            });
        }
    },

    methods: {
        handleOk() {
            this.form.get(route('admin.masterfile.store.saveUser'), {
                preserveState: true,
                onSuccess: ({ props }) => {

                    if (props.flash.success) {
                        notification.success({
                            message: props.flash.success,
                            description: 'Successfully adding new user!',
                        });
                        this.open = false;
                        this.$inertia.get(route('Admin/Masterfile/StoreStaffSetup'));
                    } else if (props.flash.error) {
                        notification.warning({
                            message: props.flash.error,
                            description: 'This username already exist, please try again!',
                        });

                    }
                },

            })
        },
        updateStoreStaffSetup(data) {
            this.openmodalforupdate = true;
            this.dataForUpdate = data;
            this.dataForUpdate.ss_username = data.ss_username;
            // Use the ID from the selected data
            // ss_username: '',
            //     ss_firstname: '',
            //         ss_lastname: '',
            //             ss_idnumber: '',
            //                 ss_store: '',
            //                     ss_usertype: '',
            //                         errors: { }

        },
        updateStoreStaff() {
            this.dataForUpdate.errors = {};
            if (!this.dataForUpdate.ss_username) {
                this.dataForUpdate.errors.ss_username = "The username field is required";
            }
            if (!this.dataForUpdate.ss_firstname) {
                this.dataForUpdate.errors.ss_firstname = "The firstname field is required";
            }
            if (!this.dataForUpdate.ss_lastname) {
                this.dataForUpdate.errors.ss_lastname = "The lastname field is required";
            }
            if (!this.dataForUpdate.ss_idnumber) {
                this.dataForUpdate.errors.ss_idnumber = "The employee ID field is required";
            }
            if (!this.dataForUpdate.ss_store) {
                this.dataForUpdate.errors.ss_store = "The store assigned field is required";
            }
            if (!this.dataForUpdate.ss_usertype) {
                this.dataForUpdate.errors.ss_usertype = "The Usertype field is required";
            }
            this.$inertia.post(route('admin.masterfile.updateStoreStaffSetup'), { ...this.dataForUpdate },
                {
                    onSuccess: ({ props }) => {
                        if (props.flash.success) {
                            notification.success({
                                message: props.flash.success,
                                description: `${this.dataForUpdate.ss_username} data updated successfully`
                            });
                            this.openmodalforupdate = false;

                        } else if (props.flash.error) {
                            notification.warning({
                                message: props.flash.error,
                                description: `${this.dataForUpdate.ss_username} data has no changes happen, update first before submitting`
                            });
                        }
                    },
                });
        },
        changePassword(rec) {
            console.log(rec);
            this.dataforChangePassword.ss_id = rec.ss_id;
            this.dataforChangePassword.ss_username = rec.ss_username;
            this.modalforchangePassword = true;
        },
        updatePassword() {

            console.log(this.dataforChangePassword.ss_id);
            this.$inertia.post(route('admin.masterfile.updateStoreStaffPassword'), {
                ...this.dataforChangePassword
            }, {
                onSuccess: ({ props }) => {
                    if (props.flash.success) {
                        notification.success({
                            message: props.flash.success,
                            description: `${this.dataforChangePassword.ss_username} password reset to default successfully!`,
                        });
                        this.modalforchangePassword = false
                    } else if (props.flash.error) {
                        notification.warning({
                            message: props.flash.error,
                            description: `${this.dataforChangePassword.ss_username} password already reset to default!`,
                        });
                        this.modalforchangePassword = false
                    }
                },
                onError: (e) => {
                    this.dataforChangePassword.errors = e;
                },
            });
        },
        changeSelectEntries(value) {
            console.log(value);
            this.$inertia.get(route('admin.masterfile.store.staff'), {
                value: value
            }, {
                preserveState: true,
                preserveScroll: true
            })
        }

    }
}
</script>
<style>
.storeStaff-button {
    text-align: right;
}

.storeStaff-input {
    background-color: #0286df;
    color: white;
    margin-right: 6%;
}

.storeStaff-search-button {
    font-weight: bold;
    text-align: right;
}

.storeStaff-search-input {
    border: 1px solid #0286df;
    width: 20%;
    margin-right: 8%;
    min-width: 120px;
    margin-top: 1%;
}
</style>
