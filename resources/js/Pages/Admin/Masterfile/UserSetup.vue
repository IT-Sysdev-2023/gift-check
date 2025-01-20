<template>
    <AuthenticatedLayout>
        <!-- BACK BUTTON  -->
        <div>
            <a-button @click="backButton">
                <RollbackOutlined /> Back
            </a-button>
        </div>

        <!-- ADD USER BUTTON  -->
        <div>
            <a-button @click="addUser" style="margin-left: 80%; background-color: #1b76f8; color:white">
                <PlusOutlined /> Add New User
            </a-button>
        </div>

        <!-- SEARCH AND HEADER  -->
        <span style="font-weight: bold; font-size: 1.5rem; font-family: sans-serif;">Users Setup</span>
        <div>
            <a-input-search @change="searchUser" v-model:value="searchUserValue" enter-button allow-clear
                placeholder="Input search here..." style="margin-left: 70%; width: 25%;" />
        </div>

        <!-- TABLE -->
        <div style="margin-top: 1rem;">
            <a-table :columns="columns" :data-source="data.data" :pagination="false" size="small">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'action'">
                        <a-button @click="updateUser(record)" title="Update User"
                            style="background-color: green; color:white">
                            <EditOutlined />
                        </a-button>
                        <a-button @click="resetUser(record)" title="Reset Password"
                            style="margin-left: 10px; background-color: #1b76f8; color:white">
                            <UndoOutlined />
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="data" class="mt-5" />
        </div>

        <!-- ADD USER MODAL  -->
        <a-modal v-model:open="addUserModal" @ok="saveNewUser">
            <div style="font-family: sans-serif; font-weight: bold; font-size: 1rem; color: #1b76f8">
                <UserAddOutlined /> Add User
            </div>
            <div style="margin-top: 2rem; font-weight: bold;">

                <!-- USERNAME  -->
                <a-form-item for="username" :validate-status="form.errors.username ? 'error' : ''"
                    :help="form.errors.username">
                    <span>Username:</span>
                    <a-input v-model:value="form.username" type="text" placeholder="Username" />
                </a-form-item>

                <!-- FIRSTNAME  -->
                <a-form-item for="firstname" :validate-status="form.errors.firstname ? 'error' : ''"
                    :help="form.errors.firstname">
                    <span>Firstname:</span>
                    <a-input v-model:value="form.firstname" type="text" placeholder="Firstname" />
                </a-form-item>

                <!-- LASTNAME  -->
                <a-form-item for="lastname" :validate-status="form.errors.lastname ? 'error' : ''"
                    :help="form.errors.lastname">
                    <span>Lastname:</span>
                    <a-input v-model:value="form.lastname" type="text" placeholder="Lastname" />
                </a-form-item>

                <!-- EMPLOYEE ID  -->
                <a-form-item for="employee_id" :validate-status="form.errors.employee_id ? 'error' : ''"
                    :help="form.errors.employee_id">
                    <span>Employee_Id:</span>
                    <a-input v-model:value="form.employee_id" type="text" placeholder="Employee_Id" />
                </a-form-item>

                <!-- USERTYPE  -->
                <a-form-item for="usertype" :validate-status="form.errors.usertype ? 'error' : ''"
                    :help="form.errors.usertype">
                    <span>Usertype:</span>
                    <a-select v-model:value="form.usertype">
                        <a-select-option v-for="item in access_page" :key="item.access_no" :value="item.access_no">
                            {{ item.title }}
                        </a-select-option>
                    </a-select>
                </a-form-item>

                <!-- USER ROLE  -->
                <a-form-item
                    v-if="[2, 3, 4, 5, 6, 7, 9, 10, 11, 13, 14].includes(form.usertype) || form.it_type === '1'"
                    for="user_role" :validate-status="form.errors.user_role ? 'error' : ''"
                    :help="form.errors.user_role">
                    <span>User Role:</span>
                    <a-select v-model:value="form.user_role">
                        <a-select-option value=1>Dept. Manager</a-select-option>
                        <a-select-option value=2>Dept. User</a-select-option>
                        <a-select-option v-if="form.usertype === 6" value=3>Releasing Personnel</a-select-option>
                    </a-select>
                </a-form-item>

                <!-- STORE ASSIGNED  -->
                <a-form-item v-if="[7, 8, 14].includes(form.usertype) || form.it_type === '2'" for="store_assigned"
                    :validate-status="form.errors.store_assigned ? 'error' : ''" :help="form.errors.store_assigned">
                    <span>Store Assigned:</span>
                    <a-select v-model:value="form.store_assigned">
                        <a-select-option v-for="item in store" :key="item.store_id" :value="item.store_id">
                            {{ item.store_name }}
                        </a-select-option>
                    </a-select>
                </a-form-item>

                <!-- RETAIL GROUP  -->
                <a-form-item v-if="form.usertype === 8" for="retail_group"
                    :validate-status="form.errors.retail_group ? 'error' : ''" :help="form.errors.retail_group">
                    <span>User Group:</span>
                    <a-select v-model:value="form.retail_group">
                        <a-select-option value=1>
                            Group 1
                        </a-select-option>
                        <a-select-option value=2>
                            Group 2
                        </a-select-option>
                    </a-select>
                </a-form-item>

                <!-- IT TYPE  -->
                <a-form-item v-if="form.usertype === 12" for="it_type"
                    :validate-status="form.errors.it_type ? 'error' : ''" :help="form.errors.it_type">
                    <span>IT Type:</span>
                    <a-select v-model:value="form.it_type">
                        <a-select-option value=1>
                            Corporate IT
                        </a-select-option>
                        <a-select-option value=2>
                            Store It
                        </a-select-option>
                    </a-select>
                </a-form-item>
            </div>
        </a-modal>

        <!-- UPDATE USER MODAL  -->
        <a-modal v-model:open="updateUserModal" @ok="saveUpdateUser">
            <div style="font-family: sans-serif; font-weight: bold; font-size: 1rem; color: #1b76f8">
                <EditOutlined /> Update User
            </div>
            <div>
                <h2 style="color:red">Not Yet Done</h2>
            </div>
            <div style="margin-top: 2rem; font-weight: bold;">

                <!-- USERNAME  -->
                <a-form-item for="username" :validate-status="updateForm.errors?.username ? 'error' : ''"
                    :help="updateForm.errors?.username">
                    <span>Username:</span>
                    <a-input v-model:value="updateForm.username" type="text" placeholder="Username" />
                </a-form-item>

                <!-- FIRSTNAME  -->
                <a-form-item for="firstname" :validate-status="updateForm.errors?.firstname ? 'error' : ''"
                    :help="updateForm.errors?.firstname">
                    <span>Firstname:</span>
                    <a-input v-model:value="updateForm.firstname" type="text" placeholder="Firstname" />
                </a-form-item>

                <!-- LASTNAME  -->
                <a-form-item for="lastname" :validate-status="updateForm.errors?.lastname ? 'error' : ''"
                    :help="updateForm.errors?.lastname">
                    <span>Lastname:</span>
                    <a-input v-model:value="updateForm.lastname" type="text" placeholder="Lastname" />
                </a-form-item>

                <!-- EMPLOYEE ID  -->
                <a-form-item for="employee_id" :validate-status="updateForm.errors?.emp_id ? 'error' : ''"
                    :help="updateForm.errors?.emp_id">
                    <span>Employee_Id:</span>
                    <a-input v-model:value="updateForm.emp_id" type="text" placeholder="Employee_Id" />
                </a-form-item>

                <!-- USERTYPE  -->
                <a-form-item for="usertype" :validate-status="updateForm.errors?.usertype ? 'error' : ''"
                    :help="updateForm.errors?.usertype">
                    <span>Usertype:</span>
                    <a-select v-model:value="updateForm.usertype">
                        <a-select-option v-for="item in access_page" :key="item.access_no" :value="item.access_no">
                            {{ item.title }}
                        </a-select-option>
                    </a-select>
                </a-form-item>

                <!-- USER ROLE  -->
                <a-form-item
                    v-if="[2, 3, 4, 5, 6, 7, 9, 10, 11, 13, 14].includes(updateForm.usertype) || updateForm.it_type === '1'"
                    for="user_role" :validate-status="updateForm.errors?.user_role ? 'error' : ''"
                    :help="updateForm.errors?.user_role">
                    <span>User Role:</span>
                    <a-select v-model:value="updateForm.user_role">
                        <a-select-option value=1>Dept. Manager</a-select-option>
                        <a-select-option value=2>Dept. User</a-select-option>
                        <a-select-option v-if="updateForm.usertype === 6" value=3>Releasing Personnel</a-select-option>
                    </a-select>
                </a-form-item>

                <!-- STORE ASSIGNED  -->
                <a-form-item v-if="[7, 8, 14].includes(updateForm.usertype) || updateForm.it_type === '2'"
                    for="store_assigned" :validate-status="updateForm.errors?.store_assigned ? 'error' : ''"
                    :help="updateForm.errors?.store_assigned">
                    <span>Store Assigned:</span>
                    <a-select v-model:value="updateForm.store_assigned">
                        <a-select-option v-for="item in store" :key="item.store_id" :value="item.store_id">
                            {{ item.store_name }}
                        </a-select-option>
                    </a-select>
                </a-form-item>

                <!-- RETAIL GROUP  -->
                <a-form-item v-if="updateForm.usertype === 8" for="retail_group"
                    :validate-status="updateForm.errors?.retail_group ? 'error' : ''"
                    :help="updateForm.errors?.retail_group">
                    <span>User Group:</span>
                    <a-select v-model:value="updateForm.retail_group">
                        <a-select-option value=1>
                            Group 1
                        </a-select-option>
                        <a-select-option value=2>
                            Group 2
                        </a-select-option>
                    </a-select>
                </a-form-item>

                <!-- IT TYPE  -->
                <a-form-item v-if="updateForm.usertype === 12" for="it_type"
                    :validate-status="updateForm.errors?.it_type ? 'error' : ''" :help="updateForm.errors?.it_type">
                    <span>IT Type:</span>
                    <a-select v-model:value="updateForm.it_type">
                        <a-select-option value=1>
                            Corporate IT
                        </a-select-option>
                        <a-select-option value=2>
                            Store It
                        </a-select-option>
                    </a-select>
                </a-form-item>
            </div>
        </a-modal>

        <a-modal v-model:open="resetPasswordModal">
            <h1>Under Construction</h1>
        </a-modal>

        <!-- {{ data }} -->
    </AuthenticatedLayout>
</template>
<script setup>
import { router } from '@inertiajs/core';
import { notification } from 'ant-design-vue';
import { reactive, ref } from 'vue';

defineProps({
    data: Object,
    access_page: Object,
    store: Object,
})
const searchUserValue = ref("");
const resetPasswordModal = ref(false);
const addUserModal = ref(false);
const updateUserModal = ref(false);

const columns = ref([
    {
        title: 'Username',
        dataIndex: 'username'
    },
    {
        title: 'Employee Id',
        dataIndex: 'emp_id'
    },
    {
        title: 'Firstname',
        dataIndex: 'firstname'

    },
    {
        title: 'Lastname',
        dataIndex: 'lastname'
    },
    {
        title: 'User Group',
        dataIndex: 'title'
    },
    {
        title: 'Store Assigned',
        dataIndex: 'store_name'
    },
    {
        title: 'Status',
        dataIndex: 'user_status'
    },
    {
        title: 'Date Created',
        dataIndex: 'date_created'
    },
    {
        title: 'Action',
        dataIndex: 'action'
    }
]);

const form = reactive({
    username: '',
    firstname: '',
    lastname: '',
    employee_id: '',
    usertype: '',
    user_role: '',
    store_assigned: '',
    retail_group: '',
    it_type: '',
    errors: {}
});

const updateForm = ref({
    username: "",
    firstname: "",
    lastname: "",
    emp_id: "",
    usertype: "",
    user_role: "",
    store_assigned: "",
    retail_group: "",
    it_type: "",
    user_id: ""
})

const backButton = () => {
    router.get(route("admin.dashboard"));
}

const addUser = () => {
    addUserModal.value = true;
}

const updateUser = (data) => {
    updateUserModal.value = true;
    updateForm.value = { ...data }
}

const resetUser = () => {
    resetPasswordModal.value = true
}

const searchUser = () => {
    router.get(route('admin.masterfile.users'), {
        searchData: searchUserValue.value
    }, {
        preserveState: true
    });
}

const saveNewUser = async () => {
    try {
        router.post(route('admin.masterfile.user.saveUser'), {
            username: form.username,
            firstname: form.firstname,
            lastname: form.lastname,
            employee_id: form.employee_id,
            usertype: form.usertype,
            user_role: form.user_role,
            store_assigned: form.store_assigned,
            retail_group: form.retail_group,
            it_type: form.it_type
        },
            {
                onSuccess: () => {
                    notification.success({
                        message: 'Success',
                        description: 'Users added successfully'
                    });
                    addUserModal.value = false;
                    form.username = '';
                    form.firstname = '';
                    form.lastname = '';
                    form.employee_id = '';
                    form.usertype = '';
                    form.user_role = '';
                    form.store_assigned = '';
                    form.retail_group = '';
                    form.it_type = '';

                },
            }
        );
    } catch (error) {
        console.error("Failed Adding User", error);
    }
}

const saveUpdateUser = async () => {
    try {
        router.post(route('admin.masterfile.updateUser'), {
            user_id: updateForm.value.user_id,
            username: updateForm.value.username,
            firstname: updateForm.value.firstname,
            lastname: updateForm.value.lastname,
            employee_id: updateForm.value.emp_id,
            usertype: updateForm.value.usertype,
            user_role: updateForm.value.user_role,
            store_assigned: updateForm.value.store_assigned,
            retail_group: updateForm.value.retail_group,
            it_type: updateForm.value.it_type
        }, {
            onSuccess: () => {
                notification.success({
                    message: 'Success',
                    description: 'Users updated successfully'
                });
                updateUserModal.value = false
            }
        });
    }
    catch (error) {
        console.error("Failed to update User", error);
    }
}
</script>
