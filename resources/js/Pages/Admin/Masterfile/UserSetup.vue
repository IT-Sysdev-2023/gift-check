<template>
    <AuthenticatedLayout>
        <a-card>
            <!-- BACK BUTTON  -->
            <div>
                <a-button @click="backButton" style="font-weight: bold;">
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

            <!-- LOADING DIVISION  -->
            <div v-if="loading" style="position: absolute; z-index: 1000; right: 0; left: 0; top: 6rem">
                <div class="spinnerContainer">
                    <div class="spinner"></div>
                    <div class="loader">
                        <p>loading</p>
                        <div class="words">
                            <span class="word">please wait...</span>
                            <span class="word">please wait...</span>
                            <span class="word">please wait...</span>
                            <span class="word">please wait...</span>
                            <span class="word">please wait...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABLE -->
            <div style="margin-top: 1rem;">
                <a-table :columns="columns" :data-source="props.data.data" :pagination="false" size="small">
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.dataIndex === 'action'">
                            <a-button @click="updateUser(record)" title="Update User"
                                style="background-color: green; color:white">
                                <EditOutlined />
                            </a-button>
                            <a-button @click="resetPassword(record)" title="Reset Password"
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
                <header style="font-weight: bold; font-size: large;">
                    <PlusOutlined /> Add User
                </header>
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

                    <!-- IT TYPE  -->
                    <a-form-item v-if="showItType" for="it_type" :validate-status="form.errors.it_type ? 'error' : ''"
                        :help="form.errors.it_type">
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

                    <!-- USER ROLE  -->
                    <a-form-item v-if="showUserRole" for="user_role"
                        :validate-status="form.errors.user_role ? 'error' : ''" :help="form.errors.user_role">
                        <span>User Role:</span>
                        <a-select v-model:value="form.user_role">
                            <a-select-option value=1>Dept. Manager</a-select-option>
                            <a-select-option value=2>Dept. User</a-select-option>
                            <a-select-option v-if="form.usertype === 6" value=3>Releasing Personnel</a-select-option>
                        </a-select>
                    </a-form-item>

                    <!-- STORE ASSIGNED  -->
                    <a-form-item v-if="showStoreAssigned" for="store_assigned"
                        :validate-status="form.errors.store_assigned ? 'error' : ''" :help="form.errors.store_assigned">
                        <span>Store Assigned:</span>
                        <a-select v-model:value="form.store_assigned">
                            <a-select-option v-for="item in store" :key="item.store_id" :value="item.store_id">
                                {{ item.store_name }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>

                    <!-- RETAIL GROUP  -->
                    <a-form-item v-if="showRetailGroup" for="retail_group"
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

                </div>
            </a-modal>

            <!-- UPDATE USER MODAL  -->
            <a-modal v-model:open="updateUserModal" @ok="saveUpdateUser">
                <header style="font-weight: bold; font-size: large;">
                    <EditOutlined /> Update User
                </header>
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
                    <a-form-item v-if="disabledUserRole" for="user_role"
                        :validate-status="updateForm.errors?.user_role ? 'error' : ''"
                        :help="updateForm.errors?.user_role">
                        <span>User Role:</span>
                        <a-select v-model:value="updateForm.user_role" allow-clear>
                            <a-select-option value=1>Dept. Manager</a-select-option>
                            <a-select-option value=2>Dept. User</a-select-option>
                            <a-select-option v-if="updateForm.usertype === 6" value=3>Releasing
                                Personnel</a-select-option>
                        </a-select>
                    </a-form-item>

                    <!-- STORE ASSIGNED  -->
                    <a-form-item v-if="disabledStoreAssigned" for="store_assigned"
                        :validate-status="updateForm.errors?.store_assigned ? 'error' : ''"
                        :help="updateForm.errors?.store_assigned">
                        <span>Store Assigned:</span>
                        <a-select v-model:value="updateForm.store_assigned" allow-clear>
                            <a-select-option v-for="item in store" :key="item.store_id" :value="item.store_id">
                                {{ item.store_name }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>

                    <!-- RETAIL GROUP  -->
                    <a-form-item v-if="disabledRetailGroup" for="retail_group"
                        :validate-status="updateForm.errors?.retail_group ? 'error' : ''"
                        :help="updateForm.errors?.retail_group">
                        <span>User Group:</span>
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
                    <a-form-item v-if="disabledItType" for="it_type"
                        :validate-status="updateForm.errors?.it_type ? 'error' : ''" :help="updateForm.errors?.it_type">
                        <span>IT Type:</span>
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
import { reactive, ref, computed, createVNode } from 'vue';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';

const props = defineProps({
    data: Object,
    access_page: Object,
    store: Object,
    search: Array
})

// UPDATE USER USERTYPE SHOW HIDE PART
const showUserRole = computed(() => {
    return form.usertype && (
        [2, 3, 4, 5, 6, 7, 9, 10, 11, 13, 14].includes(Number(form.usertype)) || form.it_type === '1'
    );
});

const disabledUserRole = computed(() => {
    return [2, '2', 3, '3', 4, '4', 5, '5', 6, '6', 7, '7', 9, '9', 10, '10', 11, '11', 13, '13', 14, '14'].includes(Number(updateForm.value.usertype))
        || [1, '1'].includes(Number(updateForm.value.it_type));
}
);

const showStoreAssigned = computed(() => {
    return form.usertype && (
        [7, 8, 14].includes(Number(form.usertype)) || form.it_type === '2'
    );
});

const disabledStoreAssigned = computed(() => {
    return [7, '7', 8, '8', 14, '14'].includes(Number(updateForm.value.usertype)) || [2, '2'].includes(Number(updateForm.value.it_type));
});

const showRetailGroup = computed(() => {
    return Number(form.usertype) === 8;
});

const disabledRetailGroup = computed(() => {
    return [8, '8'].includes(Number(updateForm.value.usertype));
});

const showItType = computed(() => {
    return Number(form.usertype) === 12;
});

const disabledItType = computed(() => {
    return [12, '12'].includes(Number(updateForm.value.usertype));
});
// END OF UPDATE USER USERTYPE SHOW HIDE PART


const searchUserValue = ref(props.search); //SEARCH VALUE
const resetPasswordModal = ref(false); //RESET PASSWORD MODAL
const addUserModal = ref(false); //ADD USER MODAL
const updateUserModal = ref(false); //UPDATE USER MODAL
const loading = ref(false); //LOADING REACTIVE STATE

// COLUMNS TABLE DATA
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
// ADD USER FORM VALUE
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
// UPDATE USER FORM VALUE
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
    errors: {}
})
// RESET PASSWORD FORM VALUE
const updatePassword = ref({
    password: "",
})
// BACK BUTTON ROUTE
const backButton = () => {
    router.get(route("admin.dashboard"));
}
// ADD USER BUTTON
const addUser = () => {
    addUserModal.value = true;
}
// UPDATE USER BUTTON
const updateUser = (data) => {
    updateUserModal.value = true;
    updateForm.value = data;
}
// RESET PASSWORD BUTTON
const resetPassword = (data) => {
    resetPasswordModal.value = true;
    updatePassword.value = data;

}
// SEARCH LOGIC AND ROUTE
const searchUser = () => {
    router.get(route('admin.masterfile.users'), {
        searchData: searchUserValue.value
    }, {
        onStart: () => {
            loading.value = true;
        },
        onSuccess: () => {
            loading.value = false;
        },
        onError: () => {
            loading.value = false;
        },
        preserveState: true
    });
}
// SAVE USER LOGIC
const saveNewUser = async () => {
    form.errors = {};
    const requiredFields = {
        username: "Username field is required",
        firstname: "Firstname field is required",
        lastname: "Lastname field is required",
        employee_id: "Employee Id field is required",
        usertype: "Usertype field is required",
        user_role: "User Role field is required",
        store_assigned: "Store Assigned field is required",
        retail_group: "Retail Group field is required",
        it_type: "IT Type field is required"
    }
    Object.entries(requiredFields).forEach(([field, message]) => {
        if (!form[field]) {
            form.errors[field] = message
        }
    });
    try {
        router.post(route('admin.masterfile.user.saveUser'), {
            ...form
        }, {
            onSuccess: (page) => {
                if (page.props.flash.success) {
                    notification.success({
                        message: 'SUCCESS',
                        description: page.props.flash.success
                    });
                    form.username = "";
                    form.firstname = '';
                    form.lastname = "";
                    form.employee_id = "";
                    form.usertype = "";
                    form.user_role = "";
                    form.store_assigned = "";
                    form.retail_group = "";
                    form.it_type = "";
                    addUserModal.value = false;

                } else if (page.props.flash.error) {
                    notification.warning({
                        message: 'WARNING',
                        description: page.props.flash.error
                    });
                    addUserModal.value = true;


                }
            }
        }
        );
    } catch (error) {
        console.error("Failed Adding User", error);
    }
}
// UPDATE USER LOGIC
const saveUpdateUser = async () => {
    updateForm.value.errors = {};
    if (!updateForm.value.username) {
        updateForm.value.errors.username = "Username field is required"
        if (!updateForm.value.firstname) {
            updateForm.value.errors.firstname = "Firstname field is required"
        }
        if (!updateForm.value.lastname) {
            updateForm.value.errors.lastname = "Lastname field is required"
        }
        if (!updateForm.value.emp_id) {
            updateForm.value.errors.emp_id = "Employee ID field is required"
        }
        if (updateForm.value.usertype === '12') {
            updateForm.value.errors.usertype = "Usertype field is required"
        }
        if (!updateForm.value.it_type) {
            console.log(updateForm.value.it_type);
            updateForm.value.errors.it_type = "IT Type field is required"
        }
        return
    }

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
</script>
<style scoped>
/* LOADING EFFECT STYLE  */
.spinnerContainer {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.spinner {
    width: 56px;
    height: 56px;
    display: grid;
    border: 4px solid #0000;
    border-radius: 50%;
    border-right-color: #299fff;
    animation: tri-spinner 1s infinite linear;
}

.spinner::before,
.spinner::after {
    content: "";
    grid-area: 1/1;
    margin: 2px;
    border: inherit;
    border-radius: 50%;
    animation: tri-spinner 2s infinite;
}

.spinner::after {
    margin: 8px;
    animation-duration: 3s;
}

@keyframes tri-spinner {
    100% {
        transform: rotate(1turn);
    }
}

.loader {
    color: #4a4a4a;
    font-family: "Poppins", sans-serif;
    font-weight: 500;
    font-size: 25px;
    -webkit-box-sizing: content-box;
    box-sizing: content-box;
    height: 40px;
    padding: 10px 10px;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    border-radius: 8px;
}

.words {
    overflow: hidden;
}

.word {
    display: block;
    height: 100%;
    padding-left: 6px;
    color: #299fff;
    animation: cycle-words 5s infinite;
}

@keyframes cycle-words {
    10% {
        -webkit-transform: translateY(-105%);
        transform: translateY(-105%);
    }

    25% {
        -webkit-transform: translateY(-100%);
        transform: translateY(-100%);
    }

    35% {
        -webkit-transform: translateY(-205%);
        transform: translateY(-205%);
    }

    50% {
        -webkit-transform: translateY(-200%);
        transform: translateY(-200%);
    }

    60% {
        -webkit-transform: translateY(-305%);
        transform: translateY(-305%);
    }

    75% {
        -webkit-transform: translateY(-300%);
        transform: translateY(-300%);
    }

    85% {
        -webkit-transform: translateY(-405%);
        transform: translateY(-405%);
    }

    100% {
        -webkit-transform: translateY(-400%);
        transform: translateY(-400%);
    }
}
</style>
