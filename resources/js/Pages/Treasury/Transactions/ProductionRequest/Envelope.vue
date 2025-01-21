<template>
    <AuthenticatedLayout>
        <Head :title="title" />
        <a-breadcrumb style="margin: 15px 0">
            <a-breadcrumb-item>
                <Link :href="route('treasury.dashboard')">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>

        <a-card title=" Revolving Budget Entry Form" class="mt-10">
            <a-form
                :model="formState"
                :label-col="{ span: 7 }"
                :wrapper-col="{ span: 15 }"
                @finish="onSubmit"
            >
                <a-row>
                    <a-col :span="10">
                        <a-form-item label="Pr. No." name="name">
                            <a-input :value="prNo" readonly />
                        </a-form-item>
                        <a-form-item
                            label="Date Requested:"
                            name="dateRequested"
                        >
                            <a-input v-model:value="currentDate" readonly />
                        </a-form-item>
                        <a-form-item label="Date Needed:" name="dateRequested">
                            <a-date-picker
                                :disabled-date="disabledDate"
                                v-model:value="formState.dateNeeded"
                            />
                        </a-form-item>

                        <a-form-item
                            label="Remarks"
                            name="name"
                            has-feedback
                            :validate-status="getErrorStatus('remarks')"
                            :help="getErrorMessage('remarks')"
                        >
                            <a-textarea
                                v-model:value="formState.remarks"
                                @input="clearError('remarks')"
                            />
                        </a-form-item>
                        <a-form-item label="Envelope Value:"> </a-form-item>
                        <a-form-item
                            :label="envVal"
                            name="name"
                            has-feedback
                            :validate-status="getErrorStatus('qty')"
                            :help="getErrorMessage('qty')"
                        >
                            <ant-input-number v-model:amount="formState.qty" />
                        </a-form-item>

                        <a-form-item label="Prepared By">
                            <a-input
                                :value="page.auth.user.full_name"
                                readonly
                            />
                        </a-form-item>
                        <div>
                            <div class="flex justify-end mx-9">
                                <a-form-item class="text-end">
                                    <a-button type="primary" html-type="submit"
                                        >Submit</a-button
                                    >
                                </a-form-item>
                            </div>
                        </div>
                    </a-col>
                    <a-col :span="14">
                        <a-card>
                            <a-row>
                                <a-col :span="8">
                                    <a-statistic
                                        title="Regular Gc Budget"
                                        :value="regularBudget"
                                    />
                                </a-col>
                                <a-col :span="8">
                                    <a-statistic
                                        title="Special Gc Budget"
                                        :value="specialBudget"
                                    />
                                </a-col>
                                <a-col :span="8">
                                    <a-statistic
                                        title="Total Budget"
                                        :value="remainingBudget"
                                    />
                                </a-col>
                            </a-row>
                        </a-card>
                    </a-col>
                </a-row>
            </a-form>
        </a-card>
        <!-- <a-modal
            v-model:open="openIframe"
            style="width: 70%; top: 50px"
            :footer="null"
            :afterClose="closeIframe"
        >
            <iframe
                class="mt-7"
                :src="stream"
                width="100%"
                height="600px"
            ></iframe>
        </a-modal> -->
    </AuthenticatedLayout>
</template>
<script lang="ts" setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs, { Dayjs } from "dayjs";
import { useForm, usePage } from "@inertiajs/vue3";
import { PageWithSharedProps } from "@/types/index";
import { onProgress } from "@/Mixin/UiUtilities";
interface FormStateGc {
    prNo: string | null;
    remarks: string;
    dateNeeded: Dayjs;
    qty: number;
}

const props = defineProps<{
    title?: string;
    prNo: string;
    remainingBudget: string;
    regularBudget: string;
    specialBudget: string;
    envVal: string;
}>();

const page = usePage<PageWithSharedProps>().props;
const currentDate = dayjs().format("MMM DD, YYYY");
const disabledDate = (current: Dayjs) => {
    // Can not select days before today and today
    return current && current < dayjs().startOf("day");
};
const formState = useForm<FormStateGc>({
    prNo: props.prNo,
    remarks: "",
    dateNeeded: dayjs(),
    qty: 0,
});


const { openLeftNotification } = onProgress();


const onSubmit = () => {
    formState.transform((data) => ({
        ...data,
        dateNeeded: data.dateNeeded.format('YYYY-MM-DD'),

    })).post(route("treasury.transactions.production.envelopSubmission"), {
        onSuccess: ({ props }) => {
            openLeftNotification(props.flash);
            if (props.flash.success) {
                formState.reset();
                // stream.value = `data:application/pdf;base64,${props.flash.stream}`;
                // openIframe.value = true;
            }
        },
    });
};

// const closeIframe = () => {
//     router.visit(route("treasury.dashboard"));
// };
const getErrorStatus = (field: string) => {
    return formState.errors[field] ? "error" : "";
};
const getErrorMessage = (field: string) => {
    return formState.errors[field];
};
const clearError = (field: string) => {
    formState.errors[field] = null;
};
</script>
