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
            <div class="mt-5 flex justify-end">
                <a-input-search @change="searchUser" v-model:value="searchUserValue" enter-button allow-clear
                    placeholder="Input search here..." class="w-1/4" />
            </div>
            <!-- TABLE -->
            <div class="mt-5">
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
                            <a-button v-else class="bg-orange-600 text-white ml-1" @click="deactivateButton(record)"
                                title="Activate User">
                                <CheckOutlined />
                            </a-button>
                            <a-button class="bg-red-600 text-white ml-1" @click="deleteUser(record)"
                                title="Delete User">
                                <DeleteOutlined />
                            </a-button>
                            <a-button title="User Details" @click="showUserDetails(record)"
                                class="ml-1 bg-white text-blue-600" type="primary">
                                <QuestionCircleOutlined />
                            </a-button>

                        </template>
                    </template>
                </a-table>
                <pagination :datarecords="data" class="mt-5" />
            </div>
            <!-- USER DETAILS MODAL  -->
            <div>
                <a-modal v-model:open="userDetailsModal" :footer="null" width="80%">
                    <div class="flex flex-col items-center">
                        <div class="text-center mb-5">
                            <a-button type="text" class="p-0">
                                <div v-if="fetchUserDetails.employee_photo != null" class="w-[200px] h-[200px]">
                                    <a-image :src="'http://172.16.161.34:8080/hrms' + fetchUserDetails.employee_photo"
                                        alt="UserImage"
                                        class="w-[200px] h-[200px] rounded-full mx-auto object-cover border-2 border-gray-200 hover:border-blue-500 cursor-pointer transition-all" />
                                </div>
                                <div v-else>
                                    <img src="/images/new.jpg" alt="UserImage"
                                        class="w-[200px] h-[200px] rounded-full mx-auto object-cover border-2 border-gray-200 hover:border-blue-500 cursor-pointer transition-all" />
                                </div>
                            </a-button>
                        </div>

                        <div v-if="fetchUserDetails.employee_name != null" class="mt-20 mb-5 text-lg font-medium">
                            <span class="font-bold italic">{{ fetchUserDetails.employee_name }}</span>
                        </div>
                        <div v-else>
                            <span class="font-bold italic">Unknown</span>
                        </div>
                    </div>
                    <a-descriptions layout="vertical" bordered :labelStyle="{ fontWeight: 'bold' }">
                        <a-descriptions-item label="Employee Business Unit">
                            {{ fetchUserDetails.employee_bunit }}
                        </a-descriptions-item>
                        <a-descriptions-item label="Employee Company">
                            {{ fetchUserDetails.employee_company }}
                        </a-descriptions-item>
                        <a-descriptions-item label="Employee Department">
                            {{ fetchUserDetails.employee_dept }}
                        </a-descriptions-item>
                        <a-descriptions-item label="Employee ID">
                            {{ fetchUserDetails.employee_id }}
                        </a-descriptions-item>
                        <a-descriptions-item label="Employee No.">
                            {{ fetchUserDetails.employee_no }}
                        </a-descriptions-item>
                        <a-descriptions-item label="Employee Pins">
                            {{ fetchUserDetails.employee_pins }}
                        </a-descriptions-item>
                        <a-descriptions-item label="Employee Position">
                            {{ fetchUserDetails.employee_position }}
                        </a-descriptions-item>
                        <a-descriptions-item label="Employee Section">
                            {{ fetchUserDetails.employee_section }}
                        </a-descriptions-item>
                        <a-descriptions label="Employee Type">
                            {{ fetchUserDetails.employee_type }}
                        </a-descriptions>
                    </a-descriptions>
                </a-modal>
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
                            placeholder="Lastname, Firstname format" @change="getEmployee"
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
                                {{ item.usertype }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>

                    <!-- User Role -->
                    <a-form-item v-if="form.usertype == 2 || form.usertype == 3 || form.usertype == 4 || form.usertype == 5 || form.usertype == 6
                        || form.usertype == 7 || form.usertype == 8 || form.usertype == 9 || form.usertype == 10 || form.usertype == 11
                        || form.usertype == 12 || form.usertype == 13" label="User Role"
                        :validate-status="form.errors.user_role ? 'error' : ''" :help="form.errors.user_role">
                        <a-select v-model:value="form.user_role">
                            <a-select-option :value="0">Dept. Manager</a-select-option>
                            <a-select-option :value="1">Dept. User</a-select-option>
                            <a-select-option v-if="form.usertype === 6" :value="2">Releasing Personnel</a-select-option>
                        </a-select>
                    </a-form-item>

                    <!-- Store Assigned -->
                    <a-form-item v-if="form.usertype == 7 || form.it_type == 2" label="Store Assigned"
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
                    <p class="text-red-600">Note: "GC2025" is the default password to every new added user.</p>
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
                        :validate-status="updateForm.errors?.userType ? 'error' : ''"
                        :help="updateForm.errors?.userType">
                        <a-select v-model:value="updateForm.userType">
                            <a-select-option v-for="item in access_page" :key="item.title" :value="item.title">
                                {{ item.usertype }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>

                    <!-- USER ROLE  -->
                    <a-form-item label="User Role" v-if="updateForm.userType != 'Administrator'" for="userRole"
                        :validate-status="updateForm.errors?.userRole ? 'error' : ''"
                        :help="updateForm.errors?.userRole">
                        <a-select v-model:value="updateForm.userRole">
                            <a-select-option value='Dept. User'>Dept. User</a-select-option>
                            <a-select-option value='Dept. Manager'>Dept. Manager</a-select-option>
                            <a-select-option v-if="updateForm.userType != 'Administrator'"
                                value='Releasing Personel'>Releasing
                                Personnel</a-select-option>
                        </a-select>
                    </a-form-item>

                    <!-- STORE ASSIGNED  -->
                    <a-form-item label="Store Assigned"
                        v-if="updateForm.userType == 'Retail Store' || updateForm.userType == 'Store Accounting' || updateForm.itType == 'Store IT'"
                        for="store_assigned" :validate-status="updateForm.errors?.storeAssigned ? 'error' : ''"
                        :help="updateForm.errors?.storeAssigned">
                        <a-select v-model:value="updateForm.storeAssigned">
                            <a-select-option v-for="item in store" :key="item.store_name" :value="item.store_name">
                                {{ item.store_name }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>

                    <!-- RETAIL GROUP  -->
                    <a-form-item label="Retail Group" v-if="updateForm.userType == 'Retail Group'" for="retail_group"
                        :validate-status="updateForm.errors?.retailGroup ? 'error' : ''"
                        :help="updateForm.errors?.retailGroup">
                        <a-select v-model:value="updateForm.retailGroup">
                            <a-select-option value='Group 1'>
                                Group 1
                            </a-select-option>
                            <a-select-option value='Group 2'>
                                Group 2
                            </a-select-option>
                        </a-select>
                    </a-form-item>

                    <!-- IT TYPE  -->
                    <a-form-item label="IT Type" v-if="updateForm.userType == 'IT Personnel '" for="it_type"
                        :validate-status="updateForm.errors?.itType ? 'error' : ''" :help="updateForm.errors?.itType">
                        <a-select v-model:value="updateForm.itType">
                            <a-select-option value='Corporate IT'>
                                Corporate IT
                            </a-select-option>
                            <a-select-option value='Store IT'>
                                Store IT
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
    search: Array,
})
const value = ref('');
const options = ref([]);
const fetchUserDetails = ref([]);

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
// this is the search function for employee name
const getEmployee = async (value) => {
    try {
        const response = await axios.get('http://172.16.161.34/api/hrms/filter/employee/name', {
            params: {
                q: value
            }
        });
        fetchUserDetails.value = response.data.data.employee[0];
        options.value = response.data.data.employee;

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

        // lastname and firstname for username format
        const nameParts = selectedEmployee.employee_name.split(", ");
        const lastname = nameParts[0];
        const firstnameFormat = nameParts[1]?.split(" ") || [];
        const firstnameFinalFormat = firstnameFormat[0] || "";

        // firstname and middlename format
        const fullName = nameParts[1]?.trim()?.split(" ") || [];
        const firstnameParts = fullName.slice(0, -1);
        const firstname = firstnameParts.join(" ");
        const middlename = fullName[fullName.length - 1] || "";

        // final username format
        const username = `${lastname.toLowerCase()}.${firstnameFinalFormat.toLowerCase()}`;

        // set the form fields
        form.value.username = username;
        form.value.firstname = firstname || "";
        form.value.lastname = lastname || "";
        form.value.middlename = middlename;
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
        dataIndex: 'userGroup'
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
        dataIndex: 'action',
    }
]);

const updateForm = ref({
    username: "",
    firstname: "",
    lastname: "",
    emp_id: "",
    userType: "",
    userRole: "",
    storeAssigned: "",
    retailGroup: "",
    itType: "",
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
    // if(updateForm.)
    try {
        router.post(route('admin.masterfile.updateUser'), {
            username: updateForm.value.username,
            firstname: updateForm.value.firstname,
            lastname: updateForm.value.lastname,
            employee_id: updateForm.value.emp_id,
            usertype: updateForm.value.userType,
            user_role: updateForm.value.userRole,
            store_assigned: updateForm.value.storeAssigned,
            retail_group: updateForm.value.retailGroup,
            status: updateForm.value.user_status,
            it_type: updateForm.value.itType,
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
                    notification.error({
                        message: 'Error',
                        description: page.props.flash.error
                    });
                    updateUserModal.value = true;
                }
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
};


const userDetailsModal = ref(false);
const showUserDetails = (record) => {
    const value = record.lastname + ", " + record.firstname;
    getEmployee(value);
    userDetailsModal.value = true;
};

const deleteUser = (record) => {
    Modal.confirm({
        title: 'Are you sure to delete this user ?',
        icon: createVNode(ExclamationCircleOutlined),
        content: createVNode(
            'div',
            {
                style: 'color:red',
            },
            'This action could cause the loss of user credentials needed to log in to the system',
        ),
        onOk() {
            router.get(route('admin.masterfile.deleteUser'), {
                id: record.user_id,
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
    });
};
</script>
