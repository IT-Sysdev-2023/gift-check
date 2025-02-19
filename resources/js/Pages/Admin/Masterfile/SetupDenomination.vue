<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { reactive, ref } from 'vue';
import { router } from '@inertiajs/core';
import { notification } from 'ant-design-vue';

const props = defineProps({
    data: Object,
    search: String
})

const columns = ref([
    {
        title: 'Denomination',
        dataIndex: 'denomination'
    },
    {
        title: 'Fad Item Number',
        dataIndex: 'denom_fad_item_number'
    },
    {
        title: 'Barcode # Start',
        dataIndex: 'denom_barcode_start'
    },
    {
        title: 'Action',
        dataIndex: 'action'
    }
])

const loading = ref(false);
// Search function
const searchData = ref(props.search);
const searchInput = () => {
    router.get(route('admin.masterfile.denominationSetup'), {
        data: searchData.value
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
const updateDenominationModal = ref(false);
const formUpdate = ref({
    denomination: "",
    denom_barcode_start: "",
    denom_fad_item_number: "",
    errors: {}
})

const updateDenomination = (data) => {
    updateDenominationModal.value = true;
    formUpdate.value = data;

}
// updating denomination function
const addDenomination = async () => {
    formUpdate.value.errors = {};
    const validation = {
        denomination: 'This field is required',
        denom_barcode_start: 'This field is required',
        denom_fad_item_number: 'This field is required'
    }

    for (const field in validation) {
        if (!formUpdate.value[field]) {
            formUpdate.value.errors[field] = validation[field]
        }
    }
    if (Object.keys(formUpdate.value.errors).length > 0) {
        return;
    }
    try {
        router.post(route('admin.masterfile.saveUpdateDenomination'), {
            ...formUpdate.value
        }, {
            onSuccess: (page) => {
                if (page.props.flash.success) {
                    notification.success({
                        message: 'SUCCESS',
                        description: page.props.flash.success
                    });
                    updateDenominationModal.value = false;
                }
                else if (page.props.flash.error) {
                    notification.error({
                        message: 'Failed',
                        description: page.props.flash.error
                    });
                }
            }
        });
    }
    catch (error) {
        console.error("Failed to update denomination", error);
    }
}

// adding denomination function
const addDenominationModal = ref(false);
const addForm = reactive({
    denomination: '',
    denom_barcode_start: '',
    errors: {}
})
const denominationSubmit = async () => {
    addForm.errors = {}
    const validation = {
        denomination: 'This field is required',
        denom_barcode_start: 'This field is required'
    }
    for (const field in validation) {
        if (!addForm[field]) {
            addForm.errors[field] = validation[field];
        }
    }
    if (Object.keys(addForm.errors).length > 0) {
        return;
    }
    try {
        router.get(route('admin.masterfile.saveDenomination'), {
            denomination: addForm.denomination,
            denom_barcode_start: addForm.denom_barcode_start
        }, {
            onSuccess: (page) => {
                if (page.props.flash.success) {
                    notification.success({
                        message: 'SUCCESS',
                        description: page.props.flash.success
                    });
                } else if (page.props.flash.error) {
                    notification.error({
                        message: 'ERROR',
                        description: page.props.flash.error
                    });
                }
            }
        })
    } catch (error) {
        console.error('Failed to add denomination', error);
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
        <!-- add denomination button  -->
        <div>
            <a-button @click="() => addDenominationModal = true"
                style="margin-left: 80%; color: white; background-color: #1b76f8;">
                <PlusOutlined /> Add Denomination
            </a-button>
        </div>
        <!-- label  -->
        <div>
            <p style="font-weight: bold; font-size: large;">Denomination Setup</p>
        </div>
        <!-- search input  -->
        <div>
            <a-input-search v-model:value="searchData" @change="searchInput" enter-button
                placeholder="input search here..." allow-clear style="width: 25%; margin-left: 70%;" />
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
        <div>
            <a-table :columns="columns" :data-source="props.data.data" :pagination="false" size="small">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'action'">
                        <a-button @click="updateDenomination(record)" style="background-color: green; color: white">
                            <EditOutlined />
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="props.data" class="mt-5" />
        </div>
        <!-- update denomination modal  -->
        <div>
            <a-modal v-model:open="updateDenominationModal" @ok="addDenomination">
                <header style="font-weight: bold; font-size: large;">
                    <EditOutlined /> Update Denomination
                </header>
                <a-form-item style="margin-top: 2rem; font-weight: bold;"
                    :validate-status="formUpdate.errors?.denomination ? 'error' : ''"
                    :help="formUpdate.errors?.denomination">
                    Denomination:
                    <a-input v-model:value="formUpdate.denomination" type="text" placeholder="Denomination" />
                </a-form-item>
                <a-form-item style="font-weight: bold;"
                    :validate-status="formUpdate.errors?.denom_fad_item_number ? 'error' : ''"
                    :help="formUpdate.errors?.denom_fad_item_number">
                    Fad Item #:
                    <a-input v-model:value="formUpdate.denom_fad_item_number" type="text" placeholder="Barcode start" />
                </a-form-item>
                <a-form-item style="font-weight: bold;"
                    :validate-status="formUpdate.errors?.denom_barcode_start ? 'error' : ''"
                    :help="formUpdate.errors?.denom_barcode_start">
                    Barcode # Start:
                    <a-input v-model:value="formUpdate.denom_barcode_start" type="text" placeholder="Barcode start" />
                </a-form-item>
            </a-modal>
        </div>
        <!-- add denomination modal  -->
        <div>
            <a-modal v-model:open="addDenominationModal" @ok="denominationSubmit">
                <header style="font-weight: bold; font-size: large;">
                    <PlusOutlined /> Add Denomination
                </header>
                <a-form-item :validate-status="addForm.errors.denomination ? 'error' : ''"
                    :help="addForm.errors.denomination" style="margin-top: 2rem; font-weight: bold;">
                    Denomination:
                    <a-input v-model:value="addForm.denomination" type="number" allow-clear
                        placeholder="Denomination" />
                </a-form-item>
                <a-form-item :validate-status="addForm.errors.denom_barcode_start ? 'error' : ''"
                    :help="addForm.errors.denom_barcode_start" style="margin-top: 2rem; font-weight: bold;">
                    Barcode # start:
                    <a-input v-model:value="addForm.denom_barcode_start" type="number" allow-clear
                        placeholder="Barcode start" />
                </a-form-item>
            </a-modal>
        </div>
    </AuthenticatedLayout>
</template>
<style scoped>
.denomination-button {
    text-align: right;
}

.denomination-input {
    background-color: #0286df;
    color: white;
    margin-right: 6%;
}

.denomination-search-button {
    font-weight: bold;
    text-align: right;
}

.denomination-search-input {
    border: 1px solid #0286df;
    width: 20%;
    margin-right: 10%;
    min-width: 110px;
    margin-top: 1%;
}

.back-button {
    font-weight: bold;
    font-family: "Poppins", sans-serif;
}

/* loading css  */
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
