<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { reactive, ref } from 'vue';
import { router } from '@inertiajs/core';
import { notification } from 'ant-design-vue';

const props = defineProps({
    data: ([
        Object,
        String
    ]),
    search: String
})
const columns = ref([
    {
        title: 'Credit Card',
        dataIndex: 'ccard_name'
    },
    {
        title: 'Date Created',
        dataIndex: 'ccard_created_formatted'
    },
    {
        title: 'Created By',
        dataIndex: 'ccard_by'
    },
    {
        title: 'Status',
        dataIndex: 'ccard_status'
    }
])
const form = reactive({
    ccard_name: "",
    errors: {}
})
const loading = ref(false);
const searchData = ref(props.search);
    // search function
const inputSearchData = () => {
    router.get(route('admin.masterfile.creditCardSetup'), {
        data: searchData.value
    }, {
        onStart: () => {
            loading.value = true;
        },
        onSuccess: () => {
            loading.value = false;
        },
        onError: () => {
            loading.value = false
        },
        preserveState: true
    });
}
// adding function
const openAddingCreditCard = ref(false);
const addCreditCard = async () => {
    form.errors = {};
    if (!form.ccard_name) {
        form.errors.ccard_name = 'This field is required';
        return;
    }
    try {
        router.get(route('admin.masterfile.saveCreditCard'), {
            ...form
        }, {
            onSuccess: (page) => {
                if (page.props.flash.success) {
                    notification.success({
                        message: 'SUCCESS',
                        description: page.props.flash.success
                    });
                    form.ccard_name = "";
                }
                else if (page.props.flash.error) {
                    notification.error({
                        message: 'FAILED',
                        description: page.props.flash.error
                    });
                }
            }
        })
    }
    catch (error) {
        console.error("Failed to add", error);
    }
}
</script>

<template>
    <AuthenticatedLayout>
        <!-- back button  -->
        <div>
            <a-button :href="route('admin.dashboard')" style="font-weight: bold;">
                <RollbackOutlined /> Back
            </a-button>
        </div>
        <!-- add button  -->
        <div>
            <a-button @click="() => (openAddingCreditCard = true)"
                style="margin-left: 80%; background-color: #1b76f8; color:white">
                <PlusOutlined /> Add Credit Card
            </a-button>
        </div>
        <!-- label  -->
        <div>
            <p style="font-weight: bold; font-size: large;">CREDIT CARD SETUP</p>
        </div>
        <!-- search input  -->
        <div>
            <a-input-search @change="inputSearchData" v-model:value="searchData" allow-clear enter-button
                placeholder="Input search here..." style="width: 25%; margin-left: 70%;" />
        </div>
        <!-- loading effect  -->
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
        <!-- table  -->
        <div style="margin-top: 1rem;">
            <a-table :data-source="props.data.data" :columns="columns" :pagination="false" size="small">
            </a-table>
            <pagination :datarecords="props.data" class="mt-5" />
        </div>
        <!-- add modal  -->
        <a-modal v-model:open="openAddingCreditCard" @ok="addCreditCard">
            <header style="font-size: large; font-weight: bold;">
                <PlusOutlined /> Add Credit Card
            </header>
            <a-form-item style="margin-top: 2rem; font-weight: bold;"
                :validate-status="form.errors.ccard_name ? 'error' : ''" :help="form.errors.ccard_name">
                Card Name:
                <a-input v-model:value="form.ccard_name" type="text" placeholder="Credit Card Name" />
            </a-form-item>
        </a-modal>
    </AuthenticatedLayout>
</template>

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
