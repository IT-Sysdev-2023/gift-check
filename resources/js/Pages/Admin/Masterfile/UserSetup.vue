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

        <a-card title="User Setup" class="mt-5">
            <!-- ADD USER BUTTON  -->
            <div class="flex justify-end">
                <a-button @click="() => (addUserModal = true)" class="bg-blue-600 justify-end text-white"
                    type="primary">
                    <PlusOutlined /> Add New User
                </a-button>
            </div>
            <!-- SEARCH -->
            <div class="mt-5">
                <a-input-search @change="searchUser" v-model:value="searchUserValue" enter-button allow-clear
                    placeholder="Input search here..." class="w-1/4 float-right" />
            </div>

            <!-- TABLE -->
            <div class="mt-10">
                <a-table :columns="columns" :data-source="props.data.data" :pagination="false" size="small">
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.dataIndex === 'action'">
                            <a-button @click="updateUser(record)" type="primary" title="Update User"
                                class="bg-green-600 text-white">
                                <EditOutlined />
                            </a-button>
                            <a-button @click="resetPassword(record)" title="Reset Password" type="primary"
                                class="bg-blue-600 text-white ml-1">
                                <UndoOutlined />
                            </a-button>
                            <a-button v-if="record.user_status === 'active'" @click="deactivateButton(record.user_id)"
                                title="Deactivate User" class="bg-red-600 text-white ml-1" type="primary">
                                <StopOutlined />
                            </a-button>
                            <a-button v-else style="margin-left: 10px; background-color: #f50; color:white"
                                @click="deactivateButton(record)" title="Activate User">
                                <CheckOutlined />
                            </a-button>
                        </template>
                    </template>
                </a-table>
                <pagination :datarecords="data" class="mt-5" />
            </div>

            <!-- ADD USER MODAL  -->
            <a-modal v-model:open="addUserModal" @ok="saveNewUser">
                <header style="font-weight: bold; font-size: large;">
                    <PlusOutlined /> Add User
                </header>
                <div style="margin-top: 2rem; font-weight: bold;">
                    <!-- Select Employee Name -->
                    <a-form-item label="Select Employee Name" :labelStyle="{ fontWeight: 'bold' }">
                        <a-auto-complete allow-clear v-model:value="value" :options="queryNames" style="width: 475px"
                            placeholder="Search lastname first..." @change="getEmployee"
                            @select="handleEmployeeSelect" />
                    </a-form-item>

                    <!-- Employee Details -->
                    <a-descriptions v-if="queryNames.length > 0" :labelStyle="{ fontWeight: 'bold' }" layout="vertical"
                        bordered size="small">
                        <a-descriptions-item label="Username">
                            {{ form.username }}
                        </a-descriptions-item>
                        <a-descriptions-item label="Firstname">
                            {{ form.firstname }}
                        </a-descriptions-item>
                        <a-descriptions-item label="Lastname">
                            {{ form.lastname }}
                        </a-descriptions-item>
                        <a-descriptions-item label="Employee ID">
                            {{ form.employee_id }}
                        </a-descriptions-item>
                    </a-descriptions>

                    <!-- User Type -->
                    <a-form-item label="Usertype" class="mt-5" :validate-status="form.errors.usertype ? 'error' : ''"
                        :help="form.errors.usertype">
                        <a-select v-model:value="form.usertype">
                            <a-select-option v-for="item in access_page" :key="item.access_no" :value="item.access_no">
                                {{ item.title }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>

                    <!-- User Role -->
                    <a-form-item v-if="form.usertype == 2 || form.usertype == 3 || form.usertype == 4 || form.usertype == 5 || form.usertype == 6
                        || form.usertype == 7 || form.usertype == 8 || form.usertype == 9 || form.usertype == 10 || form.usertype == 11
                        || form.usertype == 12 || form.usertype == 13 || form.usertype == 14" label="User Role"
                        :validate-status="form.errors.user_role ? 'error' : ''" :help="form.errors.user_role">
                        <a-select v-model:value="form.user_role">
                            <a-select-option :value="1">Dept. Manager</a-select-option>
                            <a-select-option :value="2">Dept. User</a-select-option>
                            <a-select-option v-if="form.usertype === 6" :value="3">Releasing Personnel</a-select-option>
                        </a-select>
                    </a-form-item>

                    <!-- Store Assigned -->
                    <a-form-item v-if="form.usertype == 7 || form.usertype == 14" label="Store Assigned"
                        :validate-status="form.errors.store_assigned ? 'error' : ''" :help="form.errors.store_assigned">
                        <a-select v-model:value="form.store_assigned">
                            <a-select-option v-for="item in store" :key="item.store_id" :value="item.store_id">
                                {{ item.store_name }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>

                    <!-- Retail Group -->
                    <a-form-item v-if="form.usertype == 8" label="Retail Group"
                        :validate-status="form.errors.retail_group ? 'error' : ''" :help="form.errors.retail_group">
                        <a-select v-model:value="form.retail_group">
                            <a-select-option :value="1">Group 1</a-select-option>
                            <a-select-option :value="2">Group 2</a-select-option>
                        </a-select>
                    </a-form-item>

                    <!-- IT Type -->
                    <a-form-item v-if="form.usertype == 12" label="IT Type"
                        :validate-status="form.errors.it_type ? 'error' : ''" :help="form.errors.it_type">
                        <a-select v-model:value="form.it_type">
                            <a-select-option :value="1">Corporate IT</a-select-option>
                            <a-select-option :value="2">Store IT</a-select-option>
                        </a-select>
                    </a-form-item>
                </div>
                <div>
                    <p class="text-red-600">Note: "GC2015" is the default password to every new added user.</p>
                </div>
            </a-modal>


            <!-- UPDATE USER MODAL  -->
            <a-modal v-model:open="updateUserModal" @ok="saveUpdateUser">
                <header style="font-weight: bold; font-size: large;">
                    <EditOutlined /> Update User
                </header>
                <div style="margin-top: 2rem; font-weight: bold;">

                    <!-- USERNAME  -->
                    <a-form-item label="Username" for="username"
                        :validate-status="updateForm.errors?.username ? 'error' : ''"
                        :help="updateForm.errors?.username">
                        <a-input v-model:value="updateForm.username" type="text" placeholder="Username" />
                    </a-form-item>

                    <!-- FIRSTNAME  -->
                    <a-form-item label="Firstname" for="firstname"
                        :validate-status="updateForm.errors?.firstname ? 'error' : ''"
                        :help="updateForm.errors?.firstname">
                        <a-input v-model:value="updateForm.firstname" type="text" placeholder="Firstname" />
                    </a-form-item>

                    <!-- LASTNAME  -->
                    <a-form-item label="Lastname" for="lastname"
                        :validate-status="updateForm.errors?.lastname ? 'error' : ''"
                        :help="updateForm.errors?.lastname">
                        <a-input v-model:value="updateForm.lastname" type="text" placeholder="Lastname" />
                    </a-form-item>

                    <!-- EMPLOYEE ID  -->
                    <a-form-item label="Employee ID" for="employee_id"
                        :validate-status="updateForm.errors?.emp_id ? 'error' : ''" :help="updateForm.errors?.emp_id">
                        <a-input v-model:value="updateForm.emp_id" type="text" placeholder="Employee_Id" />
                    </a-form-item>

                    <!-- USERTYPE  -->
                    <a-form-item label="Usertype" for="usertype"
                        :validate-status="updateForm.errors?.usertype ? 'error' : ''"
                        :help="updateForm.errors?.usertype">
                        <a-select v-model:value="updateForm.usertype">
                            <a-select-option v-for="item in access_page" :key="item.access_no" :value="item.access_no">
                                {{ item.title }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>

                    <!-- USER ROLE  -->
                    <a-form-item label="User Role" v-if="updateForm.usertype == 2 || updateForm.usertype == 3 || updateForm.usertype == 4 || updateForm.usertype == 5 || updateForm.usertype == 6
                        || updateForm.usertype == 7 || updateForm.usertype == 8 || updateForm.usertype == 9 || updateForm.usertype == 10 || updateForm.usertype == 11
                        || updateForm.usertype == 12 || updateForm.usertype == 13 || updateForm.usertype == 14"
                        for="user_role" :validate-status="updateForm.errors?.user_role ? 'error' : ''"
                        :help="updateForm.errors?.user_role">
                        <a-select v-model:value="updateForm.user_role" allow-clear>
                            <a-select-option value=1>Dept. Manager</a-select-option>
                            <a-select-option value=2>Dept. User</a-select-option>
                            <a-select-option v-if="updateForm.usertype != 1" value=3>Releasing
                                Personnel</a-select-option>
                        </a-select>
                    </a-form-item>

                    <!-- STORE ASSIGNED  -->
                    <a-form-item label="Store Assigned" v-if="updateForm.usertype == 7 || updateForm.usertype == 14"
                        for="store_assigned" :validate-status="updateForm.errors?.store_assigned ? 'error' : ''"
                        :help="updateForm.errors?.store_assigned">
                        <a-select v-model:value="updateForm.store_assigned" allow-clear>
                            <a-select-option v-for="item in store" :key="item.store_id" :value="item.store_id">
                                {{ item.store_name }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>

                    <!-- RETAIL GROUP  -->
                    <a-form-item label="Retail Group" v-if="updateForm.usertype == 8" for="retail_group"
                        :validate-status="updateForm.errors?.retail_group ? 'error' : ''"
                        :help="updateForm.errors?.retail_group">
                        <a-select v-model:value="updateForm.retail_group" allow-clear>
                            <a-select-option value=1>
                                Group 1
                            </a-select-option>
                            <a-select-option value=2>
                                Group 2
                            </a-select-option>
                        </a-select>
                    </a-form-item>

                    <!-- IT TYPE  -->
                    <a-form-item label="IT Type" v-if="updateForm.usertype == 12" for="it_type"
                        :validate-status="updateForm.errors?.it_type ? 'error' : ''" :help="updateForm.errors?.it_type">
                        <a-select v-model:value="updateForm.it_type" allow-clear>
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
            <!-- UPDATE PASSWORD MODAL  -->
            <a-modal v-model:open="resetPasswordModal" @ok="resetPasswordData">
                <header style="font-size: large; font-weight: bold;">
                    <UndoOutlined /> Reset Password
                </header>
                <div style="margin-top: 2rem;">
                    <span style="font-family: sans-serif; font-size: 1rem;">Reset <span
                            style="color: red; text-decoration: underline;">{{ updatePassword.full_name }}</span>
                        password
                        to
                        default?</span>
                </div>
            </a-modal>
            <!-- {{ data }} -->
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup>
import { router } from '@inertiajs/core';
import { notification, Modal } from 'ant-design-vue';
import { ref, computed, createVNode } from 'vue';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';
import axios from 'axios';

const title = ref('User Setup');
const props = defineProps({
    data: Object,
    access_page: Object,
    store: Object,
    search: Array
})
const value = ref('');
const options = ref([]);

const form = ref({
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
const getEmployee = async () => {
    try {
        const response = await axios.get('http://172.16.161.34/api/hrms/filter/employee/name', {
            params: {
                q: value.value
            }
        });
        options.value = response.data.data.employee;
        console.log(response.data.data.employee);
    }
    catch (error) {
        console.log(error);
    }
}
// this is the options display base on your search
const queryNames = computed(() => {
    return options.value.map(employee => ({
        value: employee.employee_name,
        label: employee.employee_name,
    }));
});

// formatting the form fields
const handleEmployeeSelect = (selectedValue) => {
    const selectedEmployee = options.value.find(emp => emp.employee_name === selectedValue);
    if (selectedEmployee) {
        const nameParts = selectedEmployee.employee_name.split(", ");
        const lastname = nameParts[0];
        const firstMiddle = nameParts[1]?.split(" ") || [];
        const firstname = firstMiddle[0] || "";
        const username = `${lastname.toLowerCase()}.${firstname.toLowerCase()}`;

        form.value.username = username;
        form.value.firstname = firstname || "";
        form.value.lastname = lastname || "";
        form.value.employee_id = selectedEmployee.employee_id;
    }
};

const searchUserValue = ref(props.search); //SEARCH VALUE
const resetPasswordModal = ref(false); //RESET PASSWORD MODAL
const addUserModal = ref(false); //ADD USER MODAL
const updateUserModal = ref(false); //UPDATE USER MODAL

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
    user_id: "",
    user_status: "",
    errors: {}
})
const updatePassword = ref({
    password: "",
})

const updateUser = (data) => {
    updateUserModal.value = true;
    updateForm.value = data;
}
const resetPassword = (data) => {
    resetPasswordModal.value = true;
    updatePassword.value = data;

}
const searchUser = () => {
    router.get(route('admin.masterfile.users'), {
        searchData: searchUserValue.value
    }, {
        preserveState: true
    });
}
// SAVE USER LOGIC
const saveNewUser = async () => {
    if (!value.value || !form.value.usertype) {
        notification.warning({
            message: 'Warning',
            description: 'Please fill all required fields'
        });
        return;
    }
    try {
        router.post(route('admin.masterfile.user.saveUser'), {
            ...form.value
        }, {
            onSuccess: (page) => {
                if (page.props.flash.success) {
                    notification.success({
                        message: 'SUCCESS',
                        description: page.props.flash.success
                    });
                    form.value.username = "";
                    form.value.firstname = '';
                    form.value.lastname = "";
                    form.value.employee_id = "";
                    form.value.usertype = "";
                    form.value.user_role = "";
                    form.value.store_assigned = "";
                    form.value.retail_group = "";
                    form.value.it_type = "";
                    addUserModal.value = false;

                } else if (page.props.flash.error) {
                    notification.error({
                        message: 'Error',
                        description: page.props.flash.error

                    });
                    addUserModal.value = true;
                }
            }
        }
        );
    } catch (error) {
        console.error("Failed Adding User", error);
        if (error.response && error.response.status === 422) {
            notification.warning({
                message: 'Warning',
                description: 'Please fill all required fields'
            });
        }
    }
}
// UPDATE USER LOGIC
const saveUpdateUser = async () => {
    try {
        router.post(route('admin.masterfile.updateUser'), {
            username: updateForm.value.username,
            firstname: updateForm.value.firstname,
            lastname: updateForm.value.lastname,
            employee_id: updateForm.value.emp_id,
            usertype: updateForm.value.usertype,
            user_role: updateForm.value.user_role,
            store_assigned: updateForm.value.store_assigned,
            retail_group: updateForm.value.retail_group,
            status: updateForm.value.user_status,
            it_type: updateForm.value.it_type,
            user_id: updateForm.value.user_id
        }, {
            onSuccess: (page) => {
                if (page.props.flash.success) {
                    notification.success({
                        message: 'Success',
                        description: page.props.flash.success
                    });
                    updateUserModal.value = false;
                } else if (page.props.flash.error) {
                    notification.warning({
                        message: 'Opps',
                        description: page.props.flash.error
                    });
                    updateUserModal.value = true;
                };

            }
        });
    }
    catch (error) {
        console.error("Failed to update User", error);
    }
}
// RESET PASSWORD LOGIC
const resetPasswordData = async () => {
    resetPasswordModal.value = false;
    Modal.confirm({
        title: 'Confirmation?',
        icon: createVNode(ExclamationCircleOutlined),
        content: createVNode(
            'div',
            {
                style: 'color:red;',
            },
            'Are you sure you want to reset password',
        ),
        onOk: () => {
            try {
                router.get(route('admin.masterfile.usersResetPassword'), {
                    user_id: updatePassword.value.user_id
                }, {
                    onSuccess: (page) => {
                        if (page.props.flash.success) {
                            notification.success({
                                message: 'Success',
                                description: page.props.flash.success
                            });
                            updatePassword.value = false;
                        } else if (page.props.flash.error) {
                            notification.warning({
                                message: 'OPPS',
                                description: page.props.flash.error
                            });
                        }
                    }
                });
            } catch (error) {
                console.error("Failed to reset password", error)
            }
        },
        onCancel() {
            console.log('Cancel');
        },
        class: 'test',
    });
}


const deactivateButton = (user_id) => {
    Modal.confirm({
        title: 'Confirmation?',
        icon: createVNode(ExclamationCircleOutlined),
        content: createVNode(
            'div',
            {
                style: 'color:red;',
            },
            'Are you sure to update status of this user',
        ),
        onOk() {
            router.post(route('admin.deactivateUser'), {
                id: user_id,
            }, {
                onSuccess: (page) => {
                    if (page.props.flash.success) {
                        notification.success({
                            message: 'Success',
                            description: page.props.flash.success
                        });
                    }
                }
            });
        },
        onCancel() {
            console.log('Cancel');
        },
        class: 'test',
    });
}

</script>
