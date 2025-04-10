<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router } from '@inertiajs/core';
import { notification } from 'ant-design-vue';
import { h, reactive, ref } from 'vue';
import { createVNode } from 'vue';
import { Modal } from 'ant-design-vue';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';

const props = defineProps({
    data: Object,
    store: Object,
    search: String
});
const title = ref('Store Staff');

const columns = ref([
    { title: 'Username', dataIndex: 'ss_username' },
    { title: 'Firstname', dataIndex: 'ss_firstname' },
    { title: 'Lastname', dataIndex: 'ss_lastname' },
    { title: 'Store Assigned', dataIndex: 'store_name' },
    { title: 'Emp ID No.', dataIndex: 'ss_idnumber' },
    { title: 'Usertype', dataIndex: 'ss_usertype' },
    { title: 'Date Created', dataIndex: 'ss_date_created' },
    { title: 'Status', dataIndex: 'ss_status' },
    { title: 'Action', dataIndex: 'action' },
]);

const addingForm = reactive({
    ss_username: '',
    ss_firstname: '',
    ss_lastname: '',
    ss_idnumber: '',
    ss_password: '',
    ss_store: '',
    ss_usertype: '',
    errors: {}
});

const updateForm = reactive({
    ss_username: "",
    ss_firstname: "",
    ss_lastname: "",
    ss_idnumber: "",
    ss_store: "",
    ss_usertype: "",
    ss_id: "",
    errors: {}
});

const addNewUser = ref(false);
const updateNewUser = ref(false);

const showAddNewUserModal = () => {
    addNewUser.value = true;
};

const showUpdateUserModal = (data) => {
    updateNewUser.value = true;
    Object.assign(updateForm, data);
};

const validateForm = (form, validationRules) => {
    form.errors = {};
    for (const field in validationRules) {
        if (!form[field]) {
            form.errors[field] = validationRules[field];
        }
    }
    return Object.keys(form.errors).length === 0;
};

const submitAddingUser = async () => {
    const validationRules = {
        ss_username: 'Username field is required',
        ss_firstname: 'Firstname field is required',
        ss_lastname: 'Lastname field is required',
        ss_idnumber: 'Employee ID field is required',
        ss_password: 'Password field is required',
        ss_store: 'Store Assigned field is required',
        ss_usertype: 'Usertype field is required'
    };

    if (!validateForm(addingForm, validationRules)) return;

    try {
        router.get(route('admin.masterfile.store.saveUser'), { ...addingForm }, {
            onSuccess: (page) => {
                if (page.props.flash.success) {
                    notification.success({
                        message: 'SUCCESS',
                        description: page.props.flash.success
                    });
                    addingForm.reset();
                } else if (page.props.flash.error) {
                    notification.error({
                        message: 'FAILED',
                        description: page.props.flash.error
                    });
                }
            },
            onError: () => {
                notification.error({
                    message: 'FAILED',
                    description: 'Failed to update user'
                });
            }
        });
    } catch (error) {
        console.error('Failed to add User', error);
    }
};

const submitUpdateUser = async () => {
    const validationRules = {
        ss_username: 'Username field is required',
        ss_firstname: 'Firstname field is required',
        ss_lastname: 'Lastname field is required',
        ss_idnumber: 'Employee ID field is required',
        ss_store: 'Store Assigned field is required',
        ss_usertype: 'Usertype field is required'
    };

    if (!validateForm(updateForm, validationRules)) return;

    try {
        router.post(route('admin.masterfile.updateStoreStaffSetup'), { ...updateForm }, {
            onSuccess: (page) => {
                if (page.props.flash.success) {
                    notification.success({
                        message: 'SUCCESS',
                        description: page.props.flash.success
                    });
                    updateNewUser.value = false;
                } else if (page.props.flash.error) {
                    notification.error({
                        message: 'FAILED',
                        description: page.props.flash.error
                    });
                }
            }
        });
    } catch (error) {
        console.error('Failed to update user', error);
    }
};

const resetPassword = (data) => {
    Object.assign(updateForm, data);
    Modal.confirm({
        title: 'CONFIRMATION',
        icon: createVNode(ExclamationCircleOutlined),
        content: h('div', [
            'Are you sure to reset',
            h('span', { style: 'color:red; margin-left:2px' },
                updateForm.ss_username
            ), "'s password ?"
        ]),
        onOk: async () => {
            try {
                router.post(route('admin.masterfile.updateStoreStaffPassword'), {
                    ...updateForm
                }, {
                    onSuccess: (page) => {
                        if (page.props.flash.success) {
                            notification.success({
                                message: 'SUCCESS',
                                description: page.props.flash.success
                            });
                        } else if (page.props.flash.error) {
                            notification.error({
                                message: 'FAILED',
                                description: page.props.flash.error
                            });
                        }
                    },
                    onError: (page) => {
                        if (page.props.flash.error) {
                            notification.error({
                                message: 'FAILED',
                                description: page.props.flash.error
                            });
                        }
                    }
                })
            } catch (error) {
                console.error('Failed to reset password', error);
            }
        },
        onCancel: () => {
            console.log('cancel');
        }
    });
};
const searchUser = ref(props.search);
const searchUserFunction = () => {
    router.get(route('admin.masterfile.store.staff'), {
        data: searchUser.value
    }, {
        preserveState: true
    });
}
</script>

<template>
    <AuthenticatedLayout>

        <Head :title="title" />
        <div>
            <a-breadcrumb>
                <a-breadcrumb-item>
                    <Link :href="route('admin.dashboard')">Home</Link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ title }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </div>
        <a-card title="Store Staff Setup" class="mt-5">
            <div class="flex justify-end ">
                <a-button @click="showAddNewUserModal" type="primary" class="bg-blue-600 text-white">
                    <PlusOutlined /> Add New User
                </a-button>
            </div>
            <div class=" mt-5 flex justify-end">
                <a-input-search enter-button allow-clear @change="searchUserFunction" v-model:value="searchUser"
                    placeholder="Input search here..." class="w-1/4" />
            </div>
            <div class="mt-5">
                <a-table :columns="columns" :data-source="props.data.data" :pagination="false" size="small">
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.dataIndex === 'action'">
                            <a-button title="Update" type="primary" @click="showUpdateUserModal(record)"
                                class="bg-green-600 text-white">
                                <EditOutlined />
                            </a-button>
                            <a-button title="Reset Password" type="primary" @click="resetPassword(record)"
                                class="bg-blue-600 text-white ml-1">
                                <UndoOutlined />
                            </a-button>
                        </template>
                    </template>
                </a-table>
            </div>
            <pagination :datarecords="props.data" class="mt-5" />
            <a-modal v-model:open="addNewUser" @ok="submitAddingUser">
                <header style="font-weight: bold; font-size: large;">
                    <p>
                        <PlusOutlined /> Add New User
                    </p>
                </header>
                <div style="margin-top: 2rem;">
                    <a-form-item :validate-status="addingForm.errors.ss_username ? 'error' : ''"
                        :help="addingForm.errors.ss_username" style="font-weight: bold;">
                        Username:
                        <a-input allow-clear placeholder="Username" v-model:value="addingForm.ss_username" />
                    </a-form-item>
                    <a-form-item :validate-status="addingForm.errors.ss_firstname ? 'error' : ''"
                        :help="addingForm.errors.ss_firstname" style="font-weight: bold;">
                        Firstname:
                        <a-input allow-clear placeholder="Firstname" v-model:value="addingForm.ss_firstname"
                            type="text" />
                    </a-form-item>
                    <a-form-item :validate-status="addingForm.errors.ss_lastname ? 'error' : ''"
                        :help="addingForm.errors.ss_lastname" style="font-weight: bold;">
                        Lastname:
                        <a-input allow-clear placeholder="Lastname" v-model:value="addingForm.ss_lastname"
                            type="text" />
                    </a-form-item>
                    <a-form-item :validate-status="addingForm.errors.ss_idnumber ? 'error' : ''"
                        :help="addingForm.errors.ss_idnumber" style="font-weight: bold;">
                        Employee ID:
                        <a-input-number class="w-full" allow-clear placeholder="Employee ID number"
                            v-model:value="addingForm.ss_idnumber" />
                    </a-form-item>
                    <a-form-item :validate-status="addingForm.errors.ss_password ? 'error' : ''"
                        :help="addingForm.errors.ss_password" style="font-weight: bold;">
                        Password:
                        <a-input-password allow-clear placeholder="Password" v-model:value="addingForm.ss_password" />
                    </a-form-item>
                    <a-form-item :validate-status="addingForm.errors.ss_store ? 'error' : ''"
                        :help="addingForm.errors.ss_store" style="font-weight: bold;">
                        Store Assigned:
                        <a-select v-model:value="addingForm.ss_store">
                            <a-select-option v-for="item in store" :key="item.store_id" :value="item.store_id">{{
                                item.store_name
                            }}</a-select-option>
                        </a-select>
                    </a-form-item>
                    <a-form-item :validate-status="addingForm.errors.ss_usertype ? 'error' : ''"
                        :help="addingForm.errors.ss_usertype" style="font-weight: bold;">
                        User Type:
                        <a-select v-model:value="addingForm.ss_usertype">
                            <a-select-option value="cashier">Cashier</a-select-option>
                            <a-select-option value="manager">Manager</a-select-option>
                        </a-select>
                    </a-form-item>
                </div>
            </a-modal>
            <a-modal v-model:open="updateNewUser" @ok="submitUpdateUser">
                <header style="font-weight: bold; font-size: large;">
                    <p>
                        <EditOutlined /> Update User
                    </p>
                </header>
                <div style="margin-top: 2rem;">
                    <a-form-item :validate-status="updateForm.errors.ss_username ? 'error' : ''"
                        :help="updateForm.errors.ss_username" style="font-weight: bold;">
                        Username:
                        <a-input allow-clear placeholder="Username" v-model:value="updateForm.ss_username"
                            type="text" />
                    </a-form-item>
                    <a-form-item :validate-status="updateForm.errors.ss_firstname ? 'error' : ''"
                        :help="updateForm.errors.ss_firstname" style="font-weight: bold;">
                        Firstname:
                        <a-input allow-clear placeholder="Firstname" v-model:value="updateForm.ss_firstname"
                            type="text" />
                    </a-form-item>
                    <a-form-item :validate-status="updateForm.errors.ss_lastname ? 'error' : ''"
                        :help="updateForm.errors.ss_lastname" style="font-weight: bold;">
                        Lastname:
                        <a-input allow-clear placeholder="Lastname" v-model:value="updateForm.ss_lastname"
                            type="text" />
                    </a-form-item>
                    <a-form-item :validate-status="updateForm.errors.ss_idnumber ? 'error' : ''"
                        :help="updateForm.errors.ss_idnumber" style="font-weight: bold;">
                        Employee ID:
                        <a-input allow-clear placeholder="Employee ID number" v-model:value="updateForm.ss_idnumber"
                            type="number" />
                    </a-form-item>
                    <a-form-item :validate-status="updateForm.errors.ss_store ? 'error' : ''"
                        :help="updateForm.errors.ss_store" style="font-weight: bold;">
                        Store Assigned:
                        <a-select v-model:value="updateForm.ss_store">
                            <a-select-option v-for="item in store" :key="item.store_id" :value="item.store_id">{{
                                item.store_name
                            }}</a-select-option>
                        </a-select>
                    </a-form-item>
                    <a-form-item :validate-status="updateForm.errors.ss_usertype ? 'error' : ''"
                        :help="updateForm.errors.ss_usertype" style="font-weight: bold;">
                        User Type:
                        <a-select v-model:value="updateForm.ss_usertype">
                            <a-select-option value="cashier">Cashier</a-select-option>
                            <a-select-option value="manager">Manager</a-select-option>
                        </a-select>
                    </a-form-item>
                </div>
            </a-modal>
        </a-card>
    </AuthenticatedLayout>
</template>
