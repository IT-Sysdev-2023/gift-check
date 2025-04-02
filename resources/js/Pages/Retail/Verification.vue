<template>
    <AuthenticatedLayout>
        <a-row :gutter="[16, 16]">
            <a-col :span="10">
                <a-card>
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%" label="Date">{{
                            dayjs().format('MMM, DD, YYYY')
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%" label="Verified By">{{
                            $page.props.auth.user.full_name
                            }}</a-descriptions-item>
                    </a-descriptions>

                    <a-row class="mt-5 mb-5">
                        <a-description-text keyboard>Payment To: </a-description-text>
                        <a-select size="large" ref="select" v-model:value="form.payment" style="width: 540px"
                            placeholder="Select Payment Type">
                            <a-select-option value="STORE DEPARTMENT">STORE DEPARTMENT</a-select-option>
                            <a-select-option value="WHOLESALE">WHOLESALE</a-select-option>
                        </a-select>
                    </a-row>
                    <a-row class="mb-5">
                        <a-description-text keyboard>Customer Name: </a-description-text>
                        <div class="flex direction-row gap-2 w-full">
                            <div class="w-full">
                                <a-select size="large" allow-clear show-search
                                    placeholder="Search lastname, firstname..." :default-active-first-option="false"
                                    v-model:value="form.customer" class="w-full" :show-arrow="false"
                                    :filter-option="false" :not-found-content="isRetrieving ? undefined : 'No Customer found'
                                        " :options="optionCustomer" @search="debounceCustomer">
                                    <template v-if="isRetrieving" #notFoundContent>
                                        <a-spin size="large" />
                                    </template>
                                </a-select>
                            </div>
                            <div class="w-full">
                                <a-button class="w-full" type="primary" size="large" @click="addCustomerButton">
                                    <PlusOutlined />
                                    Add Customer
                                </a-button>
                            </div>
                        </div>
                    </a-row>
                    <a-description-text keyboard>Barcode:</a-description-text>

                    <a-input-number class="pb-3 pt-3 p-1 text-xl" v-model:value="form.barcode" @change="viewstatus"
                        @keyup.enter="viewstatus" style="width: 100%" size="medium" placeholder="Scan Barcode" />
                    <a-button size="large" type="primary" class="mt-3" :disabled="form.barcode == null ||
                        form.barcode == '' ||
                        form.customer == null ||
                        form.customer == '' ||
                        form.payment == null ||
                        form.payment == ''
                        " block @click="submit" :loading="form.processing">
                        <template #icon>
                            <FastForwardOutlined />
                        </template>
                        {{
                            form.processing
                                ? "Verifying Barcode..."
                                : "VERIFY BARCODE "
                        }}
                    </a-button>
                </a-card>

                <!-- print modal after successfull verification  -->
                <!-- print modal after successful verification -->
                <a-modal v-if="dataForPrinting" v-model:open="modalOpen" style="width: 70%; top: 50px" :footer="null">
                    <iframe class="mt-7" :src="dataForPrinting" width="100%" height="600px"></iframe>
                </a-modal>

                <!-- {{ notif.data }} -->
                <a-alert v-if="notif.status" :message="notif.title" class="mb-1 mt-2" :description="notif.msg"
                    :type="notif.status" show-icon />
                <a-card v-if="notif.error == 'lost'" class="mt-5" style="
                        background-color: rgba(255, 0, 0, 0.1);
                        border: solid 1px rgba(255, 0, 0, 0.6);
                    ">
                    <p class="text-center font-bold">
                        {{ notif.msg }}
                    </p>
                    <p>
                        Issue: Barcode #
                        <span class="font-bold">{{
                            notif.data.lostgcb_barcode
                            }}</span>
                        was reported as lost
                    </p>
                    <p>
                        Owner's Name:
                        <span class="font-bold">{{
                            notif.data.lostgcd_owname
                            }}</span>
                    </p>
                    <p>
                        Address:
                        <span class="font-bold">{{
                            notif.data.lostgcd_address
                            }}</span>
                    </p>
                    <p>
                        Contact No:
                        <span class="font-bold">{{
                            notif.data.lostgcd_contactnum
                            }}</span>
                    </p>
                    <p>
                        Date Lost:
                        <span class="font-bold">{{
                            notif.data.lostgcd_datelost
                            }}</span>
                    </p>
                    <p>
                        Date Reported:
                        <span class="font-bold">{{
                            notif.data.lostgcd_datereported
                            }}</span>
                    </p>
                </a-card>
            </a-col>
            <a-col :span="13">
                <a-alert v-if="success" :message="'BARCODE # ' + form.barcode + ' FOUND'" class="mb-1" type="success"
                    show-icon />
                <a-alert v-if="notfound" :message="'BARCODE # ' + form.barcode + '  NOT FOUND'" class="mb-1" type="404"
                    show-icon />
                <a-card v-if="!empty">
                    <div v-if="skeleton">
                        <a-skeleton avatar :paragraph="{ rows: 4 }" />
                    </div>
                    <div v-else>
                        <a-steps direction="vertical" style="color: green" :current="data.length - 1" :items="data">
                        </a-steps>
                    </div>
                </a-card>
                <div v-else-if="skeleton">
                    <a-skeleton avatar :paragraph="{ rows: 4 }" />
                </div>
                <a-card v-else>
                    <a-result status="Error!" title="Warning: Scan Barcode" sub-title="In order to show barcode status">
                    </a-result>
                </a-card>
            </a-col>
        </a-row>
        <a-modal title="Add Customer" v-model:open="addCustomerModal" @ok="customerSubmit">
            <a-form-item :validate-status="addingCustomer.errors?.lastname ? 'error' : ''
                " :help="addingCustomer.errors.lastname">
                Last Name:
                <a-input allow-clear v-model:value="addingCustomer.lastname" placeholder="lastname"></a-input>
            </a-form-item>
            <a-form-item :validate-status="addingCustomer.errors?.firstname ? 'error' : ''
                " :help="addingCustomer.errors.firstname">
                First Name:
                <a-input allow-clear v-model:value="addingCustomer.firstname" placeholder="firstname"></a-input>
            </a-form-item>
            <a-form-item :validate-status="addingCustomer.errors?.middlename ? 'error' : ''
                " :help="addingCustomer.errors.middlename">
                Middle Name:
                <a-input allow-clear v-model:value="addingCustomer.middlename" placeholder="middlename"></a-input>
            </a-form-item>
            <a-form-item :validate-status="addingCustomer.errors?.extention ? 'error' : ''
                " :help="addingCustomer.errors?.extention">
                Name Extention: (ex: jr, sr, III)
                <a-input allow-clear v-model:value="addingCustomer.extention" placeholder="name extention"></a-input>
            </a-form-item>
        </a-modal>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import debounce from "lodash/debounce";
import { router, useForm } from "@inertiajs/vue3";
import { notification } from "ant-design-vue";
import dayjs from "dayjs";
import { ref } from "vue";
import axios from "axios";

const form = useForm({
    payment: null,
    customer: null,
    barcode: null,
});
defineProps({
    data: Object,
    success: Boolean,
    notfound: Boolean,
    empty: Boolean,
});

const addingCustomer = useForm({
    firstname: "",
    lastname: "",
    middlename: "",
    extention: "",
    errors: {},
});


const addCustomerModal = ref(false);
const optionCustomer = ref([]);
// const viewing = ref([]);
const notif = ref({});
const isRetrieving = ref(false);
const skeleton = ref(false);
const modalOpen = ref(false);
const viewing = ref([]);
const dataForPrinting = ref('');


const addCustomerButton = () => {
    addCustomerModal.value = true;
};

const customerSubmit = async () => {
    addingCustomer.errors = {};
    if (!addingCustomer.firstname) {
        addingCustomer.errors.firstname = "Firstname field is required";
    }
    if (!addingCustomer.lastname) {
        addingCustomer.errors.lastname = "Lastname field is required";
    }
    if (Object.keys(addingCustomer.errors).length > 0) {
        return;
    }
    try {
        const { data } = await axios.post(
            route("search.addCustomer"),
            addingCustomer,
        );
        const fullName = `${addingCustomer.lastname}, ${addingCustomer.firstname} ${addingCustomer.middlename} ${addingCustomer.extention}`;
        optionCustomer.value = [
            {
                label: fullName,
                value: data.data.cus_id,
            },
        ];
        form.customer = data.data.cus_id;

        addCustomerModal.value = false;
        addingCustomer.firstname = "";
        addingCustomer.lastname = "";
        addingCustomer.middlename = "";
        addingCustomer.extention = "";

        notification.success({
            message: "Success",
            description: "Successfully Added Customer",
        });
    } catch (error) {
        console.error("Error adding Customer", error);
        notification.error({
            message: "Error Adding",
            description: "Failed to add Customer",
        });
    }
};

const submit = () => {
    form.transform((data) => ({
        ...data,
    })).post(route("retail.verification.submit"), {
        onSuccess: (page) => {
            console.log("Flash Data:", page.props.flash);
            notif.value = page.props.flash;
            if (page.props.flash.status == 'success') {
                notification.success({
                    message: page.props.flash.title,
                    description: page.props.flash.msg,
                });
                dataForPrinting.value = `data:application/pdf;base64,${page.props.flash.data}`
                modalOpen.value = true;
                form.reset();
            } else if (page.props.flash.status == 'error') {
                notification.error({
                    message: page.props.flash.title,
                    description: page.props.flash.msg,
                });
                form.reset();

            } else if (page.props.flash.status == 'warning') {
                notification.warning({
                    message: page.props.flash.title,
                    description: page.props.flash.msg,
                });
                form.reset();
            }
        },
        onError: () => {
            form.reset();
        }
    });
};

const debounceCustomer = debounce(async function (query) {
    try {
        if (query.trim().length) {
            isRetrieving.value = true;
            optionCustomer.value = [];
            const response = await axios.get(
                route("search.customer", {
                    search: query,
                    customer: form.customer
                }),
            );
            if (response.data.success) {
                optionCustomer.value = response.data.data;
            }
            viewing.value = optionCustomer.value.length ? optionCustomer.value[0].value : null;
        }
    } catch (error) {
        console.error("Error fetching data:", error);
    } finally {
        isRetrieving.value = false;
    }
}, 500);

const viewstatus = () => {
    skeleton.value = true;
    router.get(
        route("retail.verification.index"),
        {
            barcode: form.barcode,
        },
        {
            onSuccess: () => {
                skeleton.value = false;
            },
            preserveState: true,
        },
    );
};
</script>
<style scoped></style>
