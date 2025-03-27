<script lang="ts" setup>
import { onMounted, onBeforeUnmount, ref, createVNode } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { UserType } from "@/userType";
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';
import { PageWithSharedProps } from "@/types/index";
import IadSideBar from "@/Components/IadSideBar.vue";
import AdminSidebar from "@/Components/AdminSidebar.vue";
import StoreAccountingSidebar from "@/Components/StoreAccountingSidebar.vue";
import FinanceSideBar from "@/Components/FinanceSideBar.vue";
import EodSidebar from "@/Components/EodSidebar.vue";
import CustodianSideBar from "@/Components/CustodianSideBar.vue";
import RetailGroupSidebar from "@/Components/RetailGroupSidebar.vue";
import TreasurySideBar from "@/Components/TreasurySideBar.vue";
import RetailSidebar from "@/Components/RetailSidebar.vue";
import { computed } from "vue";
import { MenuProps, notification, Modal } from "ant-design-vue";
import dayjs from "dayjs";
import MarketingSideBar from "@/Components/MarketingSideBar.vue";
import { useOnlineUsersStore } from '@/stores/online-store'
import axios from "axios";

const onlineUsersStore = useOnlineUsersStore();

const page = usePage<PageWithSharedProps>().props;

const { setOnlineUsers, addOnlineUser, removeOnlineUser } = onlineUsersStore;

onMounted(() => {
    if (page.auth) {
        window.Echo
            .join('online.users')
            .here((users) => setOnlineUsers(users))
            .joining(async (user) =>
                addOnlineUser(user))
            .leaving(async (user) => removeOnlineUser(user));
    }
});

onBeforeUnmount(() => {
    if (page.auth) {
        window.Echo.leaveAllChannels();
    }
});

const {
    treasury,
    retail,
    admin,
    finance,
    accounting,
    iad,
    custodian,
    marketing,
    retailgroup,
    eod,
    storeaccounting,
} = UserType();

const collapsed = ref<boolean>(false);
const selectedKeys = ref<string[]>(["1"]);
const curr = ref();

const dashboardRoute = computed(() => {
    const webRoute = route().current(); //get current route in page
    const res = webRoute?.split(".")[0]; // split the routes for e.g if the current route is "treasury.ledger", this would get the treasury
    return res;
});

onMounted(() => {
    curr.value = dashboardRoute.value;
});

const selectedPage: MenuProps["onClick"] = (obj) => {
    curr.value = obj.key;
    router.visit(route(obj.key + ".dashboard"));
};

// fetching HRMS user image
const currentUser = computed(() => usePage().props.auth.user);

const formattedUserName = computed(() => {
    return `${currentUser.value.lastname.toLowerCase()}, ${currentUser.value.firstname.toLowerCase()}`

});
const value = ref<string>(formattedUserName.value);
const imageUrl = ref<string>('');
const userImage = async () => {
    try {
        const response = await axios.get('http://172.16.161.34/api/hrms/filter/employee/name', {
            params: {
                q: value.value
            }
        })
        if (response.data.data.employee.length > 0) {
            imageUrl.value = response.data.data.employee[0].employee_photo;

        }
    }
    catch (error) {
        console.log(error);
    }
}
onMounted(() => {
    userImage();
});

// user defining usertype
const myProfileModal = ref<boolean>(false);

const Usertype = {

    1: 'Administrator',
    2: 'Corporate Treasury',
    3: 'Corporate Finance',
    4: 'Corporate FAD',
    5: 'General Manager',
    6: 'Corporate Marketing',
    7: 'Retail Store',
    8: 'Retail Group',
    9: 'Corporate Accounting',
    10: 'Internal Audit',
    11: 'Finance Officer',
    12: 'IT Personnel ',
    13: 'CFS',
    14: 'Store Accounting'
};
const usertype = computed(() => {
    return Usertype[page.auth.user.usertype] ?? 'Unknown';
});

// change username function
const enterPassword = ref<boolean>(false);
const changeUsernameModal = ref<boolean>(false);
const confirm = ref({
    password: ''
})
const submitUsername = () => {
    if (!username.value.username) {
        notification.error({
            message: 'Error',
            description: 'Username field is required'
        });
        return;
    }
    enterPassword.value = true;
};

const submitNow = async () => {
    try {
        const response = await axios.post(route('admin.newUsername'), {
            password: confirm.value,
            username: username.value.username,
            id: currentUser.value.user_id
        });
        if (response.data.success) {
            changeUsernameModal.value = false;
            notification.success({
                message: 'Success',
                description: response.data.message
            });
            enterPassword.value = false;
        }
        else if (response.data.error) {
            notification.error({
                message: 'Error',
                description: response.data.message
            });
        }
        console.log(response);
    }
    catch (error) {
        console.log(error);
        if (error.response && error.response.status === 422) {
            notification.error({
                message: 'Error',
                description: error.response.data.message
            });
        }
    }
}

// change password function
interface PasswordForm {
    old_password: string;
    new_password: string;
    confirm_password: string;
    id: number;
    errors: Record<string, string[]>;
}

const form = ref<PasswordForm>({
    old_password: '',
    new_password: '',
    confirm_password: '',
    id: currentUser.value.user_id,
    errors: {}
});

interface usernameForm {
    username: string;
}
const username = ref<usernameForm>({
    username: ''
})

const changePasswordModal = ref<boolean>(false);

const submitPassword = async () => {
    try {
        Modal.confirm({
            title: 'CONFIRMATION?',
            icon: createVNode(ExclamationCircleOutlined),
            content: createVNode(
                'div',
                {
                    style: 'color:red;',
                },
                'Are you sure to change your password?',
            ),
            async onOk() {
                try {
                    const response = await axios.post(route('admin.newPassword', form.value));
                    if (response.data.success) {
                        notification.success({
                            message: 'Success',
                            description: response.data.message
                        });
                        changePasswordModal.value = false;
                    }
                    else if (response.data.error) {
                        notification.error({
                            message: 'Error',
                            description: response.data.message
                        });
                    }
                } catch (error) {
                    if (error.response && error.response.data.errors) {
                        form.value.errors = error.response.data.errors;

                    } else {
                        notification.error({
                            message: 'Error',
                            description: 'Something went wrong. Please try again.'
                        });
                    }
                }

            },
            onCancel() {
                changePasswordModal.value = false;
            },
            class: 'test',
        });

    }
    catch (error) {
        console.log(error);
    }
}

</script>
<template>
    <div>
        <a-layout style="min-height: 100vh" class="dark-layout">
            <a-layout-sider v-model:collapsed="collapsed" collapsible width="250px">
                <div class="logo" />
                <a-menu v-model:selectedKeys="selectedKeys" theme="dark" mode="inline">
                    <a-card class="mb-3" v-if="!collapsed" hoverable style="
                            width: auto;
                            background: transparent;
                            border-left: none;
                            border-right: none;
                            border-top: none;
                            border-radius: 0 0 0 0px;
                        ">

                        <!-- if user user_id is 322  -->
                        <div class="flex justify-center">
                            <div v-if="page.auth.user.user_id == 322">
                                <img style="
                                        height: 80px;
                                        width: 80px;
                                        border-radius: 50%;
                                    "
                                    src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRec9vI42pnTjYGIpgq9sIzCFrlkZPmRkTCOw&s"
                                    alt="usersimage" />
                            </div>

                            <!-- user image from hrms  -->
                            <div v-else-if="imageUrl">
                                <img style="
                                        height: 100px;
                                        width: 100px;
                                        border-radius: 50%;
                                        object-fit: cover;
                                        object-position: center;
                                    " :src="'http://172.16.161.34:8080/hrms' + imageUrl" />
                            </div>

                            <!-- default user image if the user has no image found in the hrms  -->
                            <div v-else>
                                <img style="
                                height: 100px;
                                width:100px;
                                border-radius: 50%;
                                object-fit: cover;
                                object-position: center;" src="/images/new.jpg" />
                            </div>
                        </div>

                        <!-- User greetings  -->
                        <div class="text-white font-bold text-center mt-1">
                            <div v-if="formattedUserName === 'abarquez, kent'">
                                <span class="italic">Konnichiwa Master,</span> <span class="text-bold">{{
                                    page.auth.user.full_name }}</span>
                            </div>
                            <div v-else-if="formattedUserName == 'gamale, teofredo'">
                                <span class="italic">Musta Boss,</span> <span class="text-bold">{{
                                    page.auth.user.full_name }}</span>
                            </div>
                            <div v-else-if="formattedUserName == 'barace, harvey'">
                                <span class="italic"> Sawasdee Krub,</span> <span class="font-bold">{{
                                    page.auth.user.full_name }}</span>
                            </div>
                            <div v-else-if="formattedUserName == 'palban, jessan'">
                                <span class="italic"> Hello Master,</span> <span class="font-bold">{{
                                    page.auth.user.full_name }}</span>
                            </div>
                            <div v-else-if="formattedUserName == 'caren, norien'">
                                <span class="italic">Annyeong Haseyo,</span>
                                {{ page.auth.user.full_name }}
                            </div>
                            <div v-else-if="formattedUserName == 'lansang, pearl'">
                                <span class="italic">Konnichiwa,</span>
                                {{ page.auth.user.full_name }}
                            </div>
                            <div v-else-if="formattedUserName == 'cagas, claire'">
                                <span class="italic">Nǐ hǎo,</span>
                                {{ page.auth.user.full_name }}
                            </div>
                            <div v-else>
                                <span class="italic"> Hello,</span> <span class="font-bold">{{ page.auth.user.full_name
                                }}</span>
                            </div>
                        </div>

                        <!-- User details modal  -->
                        <a-modal v-model:open="myProfileModal" width="50%" :footer="null">
                            <div v-if="currentUser">
                                <a-descriptions title="User Details" :labelStyle="{ fontWeight: 'bold' }" bordered
                                    layout="vertical">
                                    <a-descriptions-item label="Username">
                                        {{ currentUser.username }}
                                    </a-descriptions-item>
                                    <a-descriptions-item label="ID Number">
                                        {{ currentUser.emp_id }}
                                    </a-descriptions-item>
                                    <a-descriptions-item label="Firstname">
                                        {{ currentUser.firstname }}
                                    </a-descriptions-item>
                                    <a-descriptions-item label="Lastname">
                                        {{ currentUser.lastname }}
                                    </a-descriptions-item>
                                    <a-descriptions-item label="User Type">
                                        {{ usertype }}
                                    </a-descriptions-item>
                                    <a-descriptions-item label="Date Created">
                                        {{ dayjs(currentUser.date_created).format('MMMM D, YYYY h:mm A') }}
                                    </a-descriptions-item>
                                </a-descriptions>
                            </div>
                        </a-modal>

                        <!-- change username modal  -->
                        <a-modal v-model:open="changeUsernameModal" with="50%" title="Change Username Setup"
                            @ok="submitUsername">
                            <a-descriptions class="mt-5" :labelStyle="{ fontWeight: 'bold' }" bordered
                                layout="horizontal">
                                <a-descriptions-item label="Current Username">
                                    {{ currentUser.username }}
                                </a-descriptions-item>
                            </a-descriptions>
                            <a-form-item label="New Username" class="mt-5">
                                <a-input v-model:value="username.username" placeholder="Input new username" />
                            </a-form-item>
                            <div class="mt-10">
                                <p class="text-red-600 text-sm">Note: Please be careful when changing your username.
                                    Make sure
                                    to remember it.</p>
                            </div>
                        </a-modal>

                        <!-- change password modal  -->
                        <a-modal v-model:open="changePasswordModal" title="Change Password Setup" @ok="submitPassword">
                            <a-form-item :validate-status="form.errors.old_password ? 'error' : ''"
                                :help="form.errors.old_password" label="Old Password" class="mt-10">
                                <a-input v-model:value="form.old_password" type="password" placeholder="Old password" />
                            </a-form-item>
                            <a-form-item :validate-status="form.errors.new_password ? 'error' : ''"
                                :help="form.errors.new_password" label="New Password">
                                <a-input v-model:value="form.new_password" type="password" placeholder="New password" />
                            </a-form-item>
                            <a-form-item :validate-status="form.errors.confirm_password ? 'error' : ''"
                                :help="form.errors.confirm_password" label="Confirm Password">
                                <a-input v-model:value="form.confirm_password" type="password"
                                    placeholder="Confirm password" />
                            </a-form-item>
                            <div class="mt-16">
                                <p class="text-red-600 text-sm">Note: Please be careful when changing your password.
                                    Make sure
                                    to remember it.</p>
                            </div>
                        </a-modal>
                        <!-- confirmation password before changes username  -->
                        <a-modal v-model:open="enterPassword" title="Enter Password for Confirmation" @ok="submitNow">
                            <a-form-item>
                                <a-input v-model:value="confirm.password" type="password" placeholder="Password" />
                            </a-form-item>
                        </a-modal>

                        <!-- Account dropdown  -->
                        <div class="flex justify-center mt-5">
                            <a-dropdown>
                                <a class="ant-dropdown-link text-white bg-blue-800 px-4 py-2" @click.prevent>
                                    My Account
                                    <DownOutlined />
                                </a>
                                <template #overlay>
                                    <a-menu>
                                        <a-menu-item>
                                            <a @click="() => (myProfileModal = true)">My Profile</a>
                                        </a-menu-item>
                                        <a-menu-item>
                                            <a @click="() => (changeUsernameModal = true)">Change Username</a>
                                        </a-menu-item>
                                        <a-menu-item>
                                            <a @click="() => { changePasswordModal = true }">Change Password</a>
                                        </a-menu-item>
                                    </a-menu>
                                </template>
                            </a-dropdown>
                        </div>
                    </a-card>
                    <div v-else>
                        <div class="flex justify-center mt-3 mb-5">
                            <img style="
                                    width: 50px;
                                    height: 50px;
                                    border-radius: 50%;
                                "
                                src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSRBlr9nmDwG7kYOIKpEVLwj-99AUlYoiohLA&s"
                                alt="logo" />
                        </div>
                    </div>

                    <store-accounting-sidebar v-if="storeaccounting" />
                    <AdminSidebar v-if="page.auth.user.usertype !== '9' && (admin && curr === 'admin')" />
                    <TreasurySideBar v-if="page.auth.user.usertype !== '9' && (treasury || curr === 'treasury')" />
                    <FinanceSideBar v-if="finance || curr === 'finance'" />
                    <CustodianSideBar v-if="page.auth.user.usertype !== '9' && (custodian || curr === 'custodian')" />
                    <RetailSidebar v-if="page.auth.user.usertype !== '9' && (retail || curr == 'retail')" />
                    <AccountingSideBar
                        v-if="page.auth.user.usertype !== '13' && accounting || (curr === 'accounting' && curr !== 'custodian')" />

                    <MarketingSideBar v-if="marketing || curr == 'marketing'" />
                    <IadSideBar v-if="iad || curr == 'iad'" />
                    <eod-sidebar v-if="eod || curr == 'eod'" />

                    <RetailGroupSidebar v-if="retailgroup || curr == 'retailgroup'" />

                    <a-menu-item key="menu-item-user-guide" @click="() => router.get(route('UserGuide'))">
                        <UserOutlined />
                        <span>User Guide</span>
                    </a-menu-item>

                    <a-menu-item key="menu-item-about-us" @click="() => router.get(route('AboutUs'))">
                        <InfoCircleOutlined />
                        <span>About Us</span>
                    </a-menu-item>
                </a-menu>
            </a-layout-sider>
            <a-layout class="layout">
                <a-layout>
                    <a-layout-header theme="dark" style="display: flex; justify-content: end">
                        <a-menu theme="dark" mode="horizontal" :style="{ lineHeight: '64px', display: 'flex' }"
                            @click="selectedPage">
                            <a-sub-menu key="dashboards" v-if="page.auth.user.usertype == '1'">
                                <template #icon>
                                    <split-cells-outlined />
                                </template>
                                <template #title>Dashboards</template>

                                <a-menu-item-group title="Dashboards">
                                    <a-menu-item key="admin">
                                        <template #icon>
                                            <file-excel-outlined />
                                        </template>
                                        Admin
                                    </a-menu-item>
                                    <a-menu-item key="treasury">
                                        <template #icon>
                                            <file-excel-outlined />
                                        </template>
                                        Treasury
                                    </a-menu-item>
                                    <a-menu-item key="finance">
                                        <template #icon>
                                            <file-excel-outlined />
                                        </template>
                                        Finance
                                    </a-menu-item>
                                    <a-menu-item key="custodian">
                                        <template #icon>
                                            <file-excel-outlined />
                                        </template>
                                        Custodian
                                    </a-menu-item>
                                    <a-menu-item key="marketing">
                                        <template #icon>
                                            <file-excel-outlined />
                                        </template>
                                        Marketing
                                    </a-menu-item>
                                    <a-menu-item key="retail">
                                        <template #icon>
                                            <file-excel-outlined />
                                        </template>
                                        Retail
                                    </a-menu-item>
                                    <a-menu-item key="retailgroup">
                                        <template #icon>
                                            <file-excel-outlined />
                                        </template>
                                        Retail Group
                                    </a-menu-item>
                                    <a-menu-item key="accounting">
                                        <template #icon>
                                            <file-excel-outlined />
                                        </template>
                                        Accounting
                                    </a-menu-item>
                                    <a-menu-item key="iad">
                                        <template #icon>
                                            <file-excel-outlined />
                                        </template>
                                        Iad
                                    </a-menu-item>
                                    <a-menu-item key="eod">
                                        <template #icon>
                                            <file-excel-outlined />
                                        </template>
                                        Eod
                                    </a-menu-item>
                                </a-menu-item-group>
                            </a-sub-menu>
                        </a-menu>
                        <p class="space-x-5">
                            <Link class="text-white" :href="page.auth.user.usertype == '1'
                                ? route('admin.dashboard')
                                : route(dashboardRoute + '.dashboard')
                                ">
                            <HomeOutlined />
                            Home
                            </Link>
                            <a-button class="text-white" type="ghost" @click="() => router.post(route('logout'))">
                                <PoweroffOutlined />Logout
                            </a-button>
                        </p>
                    </a-layout-header>
                    <a-layout-content :style="{
                        padding: '24px',
                        background: '#fff',
                        minHeight: '280px',
                    }">
                        <slot />
                    </a-layout-content>
                </a-layout>

                <footer class="bg-white dark:bg-gray-900">
                    <div class="mx-auto w-full max-w-screen max-h-6">
                        <div class="px-4 py-6 bg-gray-100 dark:bg-gray-700 md:flex md:items-center md:justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-300 sm:text-center">© 2024 - {{
                                dayjs().year() }}
                                <a href="/">Gift Check</a>. All Rights Reserved.
                            </span>
                            <div class="flex mt-4 sm:justify-center md:mt-0 space-x-5 rtl:space-x-reverse">
                                <a href="https://github.com/IT-Sysdev-2023/gift-check" target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 .333A9.911 9.911 0 0 0 6.866 19.65c.5.092.678-.215.678-.477 0-.237-.01-1.017-.014-1.845-2.757.6-3.338-1.169-3.338-1.169a2.627 2.627 0 0 0-1.1-1.451c-.9-.615.07-.6.07-.6a2.084 2.084 0 0 1 1.518 1.021 2.11 2.11 0 0 0 2.884.823c.044-.503.268-.973.63-1.325-2.2-.25-4.516-1.1-4.516-4.9A3.832 3.832 0 0 1 4.7 7.068a3.56 3.56 0 0 1 .095-2.623s.832-.266 2.726 1.016a9.409 9.409 0 0 1 4.962 0c1.89-1.282 2.717-1.016 2.717-1.016.366.83.402 1.768.1 2.623a3.827 3.827 0 0 1 1.02 2.659c0 3.807-2.319 4.644-4.525 4.889a2.366 2.366 0 0 1 .673 1.834c0 1.326-.012 2.394-.012 2.72 0 .263.18.572.681.475A9.911 9.911 0 0 0 10 .333Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span class="sr-only">GitHub account</span>
                                </a>
                                <a href="https://guthib.com" target="_blank" rel="noopener noreferrer"
                                    class="text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor" viewBox="0 0 21 16">
                                        <path
                                            d="M16.942 1.556a16.3 16.3 0 0 0-4.126-1.3 12.04 12.04 0 0 0-.529 1.1 15.175 15.175 0 0 0-4.573 0 11.585 11.585 0 0 0-.535-1.1 16.274 16.274 0 0 0-4.129 1.3A17.392 17.392 0 0 0 .182 13.218a15.785 15.785 0 0 0 4.963 2.521c.41-.564.773-1.16 1.084-1.785a10.63 10.63 0 0 1-1.706-.83c.143-.106.283-.217.418-.33a11.664 11.664 0 0 0 10.118 0c.137.113.277.224.418.33-.544.328-1.116.606-1.71.832a12.52 12.52 0 0 0 1.084 1.785 16.46 16.46 0 0 0 5.064-2.595 17.286 17.286 0 0 0-2.973-11.59ZM6.678 10.813a1.941 1.941 0 0 1-1.8-2.045 1.93 1.93 0 0 1 1.8-2.047 1.919 1.919 0 0 1 1.8 2.047 1.93 1.93 0 0 1-1.8 2.045Zm6.644 0a1.94 1.94 0 0 1-1.8-2.045 1.93 1.93 0 0 1 1.8-2.047 1.918 1.918 0 0 1 1.8 2.047 1.93 1.93 0 0 1-1.8 2.045Z" />
                                    </svg>
                                    <span class="sr-only">Discord community</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </footer>
            </a-layout>
            <ant-float v-if="treasury && page.pendingPrRequest.length" />

            <generated-report-float />
        </a-layout>
    </div>
</template>

<style scoped>
#components-layout-demo-side .logo {
    height: 32px;
    margin: 16px;
    background: rgba(255, 255, 255, 0.3);
}

.site-layout .site-layout-background {
    background: #fff;
}

[data-theme="dark"] .site-layout .site-layout-background {
    background: #141414;
}
</style>
