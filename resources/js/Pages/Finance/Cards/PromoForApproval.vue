<template>
    <a-row :gutter="[16, 16]">
        <a-col :span="11">
            <div v-if="details.data[0]?.pgcreq_group_status === 'approved'">
                <a-card>
                    <a-form layout="vertical">
                        <a-form-item>
                            <a-descriptions size="small" bordered>
                                <a-descriptions-item style="width: 50%;" label="Date Approved/Cancel:"
                                    class="text-end">{{
                                        details.data[0].today
                                    }}</a-descriptions-item>
                            </a-descriptions>
                        </a-form-item>

                        <a-form-item label="Request Status" has-feedback :validate-status="errors.status ? 'error' : ''"
                            :help="errors.status">
                            <a-select v-model:value="form.status" ref="select" placeholder="Select Status"
                                @change="() => errors.status = null">
                                <a-select-option :value="1">Approved</a-select-option>
                                <a-select-option :value="2">Cancel</a-select-option>
                            </a-select>
                        </a-form-item>
                        <a-row :gutter="[16, 16]">
                            <a-col v-if="form.status === 1" :span="12">
                                <a-form-item label="Checked By:" has-feedback
                                    :validate-status="errors.checkby ? 'error' : ''" :help="errors.checkby">
                                    <a-input v-model:value="form.checkby" readonly />
                                </a-form-item>
                            </a-col>
                            <a-col v-if="form.status === 1 || form.status === 2" :span="form.status === 1 ? 12 : 24">
                                <a-form-item has-feedback :validate-status="errors.appby ? 'error' : ''"
                                    :help="errors.appby">
                                    <template #label>
                                        {{ form.status === 1 ? 'Approved By:' : 'Cancelled By' }}
                                    </template>
                                    <a-input readonly :value="$page.props.auth.user.full_name"></a-input>
                                    <!-- <a-select ref="select" v-model:value="form.appby" placeholder="Select Approved By"
                                    @change="() => errors.appby = null">
                                    <a-select-option v-for="item in cdata" :value="item.assig_id">{{ item.assig_name
                                        }}</a-select-option>
                                </a-select> -->
                                </a-form-item>
                            </a-col>
                        </a-row>
                        <a-form-item has-feedback :help="errors.remarks" v-if="form.status === 1"
                            :validate-status="errors.remarks ? 'error' : ''">
                            <a-textarea v-model:value="form.remarks" placeholder="Enter remarks" :rows="2"
                                @change="() => errors.remarks = null" />
                        </a-form-item>
                        <div v-if="form.status === 1">
                            <a-divider>
                                <a-typography-text code>upload image</a-typography-text>
                            </a-divider>
                            <div class="flex justify-center">
                                <a-form-item>
                                    <ant-upload-image @handle-change="handleImageChange" />
                                </a-form-item>
                            </div>
                        </div>
                        <a-form-item v-if="form.status === 1 || form.status === 2">
                            <a-button type="primary" block @click="approve" :loading="form.processing">
                                <template #icon>
                                    <TagsFilled />
                                </template>
                                Submit Approval Form
                            </a-button>
                        </a-form-item>

                    </a-form>
                </a-card>
            </div>
            <div v-else>
                <a-alert message="Approval" type="warning" show-icon>
                    <template #description>
                        Promo GC Request needs Retail Group {{ details.data[0].pgcreq_group }} Approval
                    </template>
                </a-alert>
                <div class="container mt-10">
                    <div class="loader">
                        <div class="truckWrapper">
                            <div class="truckBody">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 198 93"
                                    class="trucksvg">
                                    <path stroke-width="3" stroke="#282828" fill="#F83D3D"
                                        d="M135 22.5H177.264C178.295 22.5 179.22 23.133 179.594 24.0939L192.33 56.8443C192.442 57.1332 192.5 57.4404 192.5 57.7504V89C192.5 90.3807 191.381 91.5 190 91.5H135C133.619 91.5 132.5 90.3807 132.5 89V25C132.5 23.6193 133.619 22.5 135 22.5Z">
                                    </path>
                                    <path stroke-width="3" stroke="#282828" fill="#7D7C7C"
                                        d="M146 33.5H181.741C182.779 33.5 183.709 34.1415 184.078 35.112L190.538 52.112C191.16 53.748 189.951 55.5 188.201 55.5H146C144.619 55.5 143.5 54.3807 143.5 53V36C143.5 34.6193 144.619 33.5 146 33.5Z">
                                    </path>
                                    <path stroke-width="2" stroke="#282828" fill="#282828"
                                        d="M150 65C150 65.39 149.763 65.8656 149.127 66.2893C148.499 66.7083 147.573 67 146.5 67C145.427 67 144.501 66.7083 143.873 66.2893C143.237 65.8656 143 65.39 143 65C143 64.61 143.237 64.1344 143.873 63.7107C144.501 63.2917 145.427 63 146.5 63C147.573 63 148.499 63.2917 149.127 63.7107C149.763 64.1344 150 64.61 150 65Z">
                                    </path>
                                    <rect stroke-width="2" stroke="#282828" fill="#FFFCAB" rx="1" height="7" width="5"
                                        y="63" x="187"></rect>
                                    <rect stroke-width="2" stroke="#282828" fill="#282828" rx="1" height="11" width="4"
                                        y="81" x="193"></rect>
                                    <rect stroke-width="3" stroke="#282828" fill="#DFDFDF" rx="2.5" height="90"
                                        width="121" y="1.5" x="6.5"></rect>
                                    <rect stroke-width="2" stroke="#282828" fill="#DFDFDF" rx="2" height="4" width="6"
                                        y="84" x="1">
                                    </rect>
                                </svg>
                            </div>
                            <div class="truckTires">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 30 30" class="tiresvg">
                                    <circle stroke-width="3" stroke="#282828" fill="#282828" r="13.5" cy="15" cx="15">
                                    </circle>
                                    <circle fill="#DFDFDF" r="7" cy="15" cx="15"></circle>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 30 30" class="tiresvg">
                                    <circle stroke-width="3" stroke="#282828" fill="#282828" r="13.5" cy="15" cx="15">
                                    </circle>
                                    <circle fill="#DFDFDF" r="7" cy="15" cx="15"></circle>
                                </svg>
                            </div>
                            <div class="road"></div>

                            <svg xml:space="preserve" viewBox="0 0 453.459 453.459"
                                xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg"
                                id="Capa_1" version="1.1" fill="#000000" class="lampPost">
                                <path d="M252.882,0c-37.781,0-68.686,29.953-70.245,67.358h-6.917v8.954c-26.109,2.163-45.463,10.011-45.463,19.366h9.993
c-1.65,5.146-2.507,10.54-2.507,16.017c0,28.956,23.558,52.514,52.514,52.514c28.956,0,52.514-23.558,52.514-52.514
c0-5.478-0.856-10.872-2.506-16.017h9.992c0-9.354-19.352-17.204-45.463-19.366v-8.954h-6.149C200.189,38.779,223.924,16,252.882,16
c29.952,0,54.32,24.368,54.32,54.32c0,28.774-11.078,37.009-25.105,47.437c-17.444,12.968-37.216,27.667-37.216,78.884v113.914
h-0.797c-5.068,0-9.174,4.108-9.174,9.177c0,2.844,1.293,5.383,3.321,7.066c-3.432,27.933-26.851,95.744-8.226,115.459v11.202h45.75
v-11.202c18.625-19.715-4.794-87.527-8.227-115.459c2.029-1.683,3.322-4.223,3.322-7.066c0-5.068-4.107-9.177-9.176-9.177h-0.795
V196.641c0-43.174,14.942-54.283,30.762-66.043c14.793-10.997,31.559-23.461,31.559-60.277C323.202,31.545,291.656,0,252.882,0z
M232.77,111.694c0,23.442-19.071,42.514-42.514,42.514c-23.442,0-42.514-19.072-42.514-42.514c0-5.531,1.078-10.957,3.141-16.017
h78.747C231.693,100.736,232.77,106.162,232.77,111.694z"></path>
                            </svg>
                        </div>
                    </div>

                </div>

            </div>
        </a-col>
        <a-col :span="13">
            <a-card>
                <a-statistic :value="details.data[0].current" :precision="2" class="demo-class mb-5"
                    :value-style="{ color: '#3DC2EC' }">
                    <template #title>
                        <AreaChartOutlined /> Current Available Budget
                    </template>
                    <template #prefix>
                        <LikeOutlined />
                    </template>
                </a-statistic>
                <div class="flex justify-center">
                    <a-typography-text class="mb-1 " keyboard>Promo Gc Request Details</a-typography-text>
                </div>
                <div class="mt-2">
                    <a-descriptions size="small" bordered>
                        <a-descriptions-item label="RFPROM" span style="width: 50%;">{{ details.data[0].pgcreq_reqnum
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" bordered>
                        <a-descriptions-item label="Department" style="width: 50%;">{{ details.data[0].title
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" bordered>
                        <a-descriptions-item label="Retail Group" style="width: 50%;">Group {{
                            details.data[0].pgcreq_group
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" bordered>
                        <a-descriptions-item label="Date Requested" style="width: 50%;">{{
                            details.data[0].pgcreq_datereq
                            }}
                        </a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" bordered>
                        <a-descriptions-item label="Time Requested:" style="width: 50%;">{{ details.data[0].time_req
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" bordered>
                        <a-descriptions-item label="Date Needed" style="width: 50%;">{{ details.data[0].dateneeded
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" bordered>
                        <a-descriptions-item label="Total GC Budget:" style="width: 50%;">{{ details.data[0].total
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" bordered>
                        <a-descriptions-item label="Remarks:" style="width: 50%;">{{ details.data[0].remarks }}
                        </a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" bordered>
                        <a-descriptions-item label="Prepared by:" style="width: 50%;">{{ details.data[0].fullname
                            }}</a-descriptions-item>
                    </a-descriptions>
                </div>
            </a-card>

            <a-card class="mt-2">
                <a-table size="small" bordered :pagination="false" class="mt-1" :columns="[
                    {
                        title: 'Denomination',
                        dataIndex: 'denom',
                        key: 'denom',
                    },
                    {
                        title: 'Quantity',
                        dataIndex: 'pgcreqi_qty',
                        align: 'center'

                    },
                    {
                        title: 'Subtotal',
                        dataIndex: 'subtotal',
                        align: 'start'
                    },
                ]" :data-source="denomination.data">
                </a-table>
                <div class="mt-2 flex justify-end">
                    <a-typography-text class="mt-2" keyboard>Total: {{ denomination.total }} </a-typography-text>
                </div>
            </a-card>
        </a-col>
    </a-row>

</template>
<script>
import axios from 'axios';
import pickBy from "lodash/pickBy";
import { notification } from 'ant-design-vue';
import { useForm } from '@inertiajs/vue3';
export default {
    props: {
        details: Object,
        denomination: Object,
        reqid: Number,
    },
    data() {
        return {
            cdata: [],
            errors: {},

            form: useForm({
                appby: this.$page.props.auth.user.full_name,
                checkby: this.details.data[0].checkby,
                status: null,
                remarks: null,
                reqid: this.reqid,
                file: null,
            })
        }
    },
    created() {
        this.getCheckBy();
    },
    methods: {
        getCheckBy() {
            axios.get(route('search.checkBy'))
                .then(response => {
                    this.cdata = response.data;
                })
        },
        approve() {
            this.form.transform((data) => ({
                ...pickBy(data)
            })).post(route('finance.approve.request'), {

                onSuccess: (res) => {
                    notification[res.props.flash.status]({
                        message: res.props.flash.title,
                        description: res.props.flash.msg,
                    });
                },

                onError: (e) => {
                    this.errors = e;

                    notification['error']({
                        message: 'Required',
                        description: 'Some Fields are Missing',
                    });
                }
            })
        },
        handleImageChange(file) {
            this.form.file = file.file;
        }

    },
}
</script>
<style scoped>
.container {
    display: flex;
    justify-content: center;
}

.loader {
    width: fit-content;
    height: fit-content;
    display: flex;
    align-items: center;
    justify-content: center;
}

.truckWrapper {
    width: 200px;
    height: 100px;
    display: flex;
    flex-direction: column;
    position: relative;
    align-items: center;
    justify-content: flex-end;
    overflow-x: hidden;
}

/* truck upper body */
.truckBody {
    width: 130px;
    height: fit-content;
    margin-bottom: 6px;
    animation: motion 1s linear infinite;
}

/* truck suspension animation*/
@keyframes motion {
    0% {
        transform: translateY(0px);
    }

    50% {
        transform: translateY(3px);
    }

    100% {
        transform: translateY(0px);
    }
}

/* truck's tires */
.truckTires {
    width: 130px;
    height: fit-content;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0px 10px 0px 15px;
    position: absolute;
    bottom: 0;
}

.truckTires svg {
    width: 24px;
}

.road {
    width: 100%;
    height: 1.5px;
    background-color: #282828;
    position: relative;
    bottom: 0;
    align-self: flex-end;
    border-radius: 3px;
}

.road::before {
    content: "";
    position: absolute;
    width: 20px;
    height: 100%;
    background-color: #282828;
    right: -50%;
    border-radius: 3px;
    animation: roadAnimation 1.4s linear infinite;
    border-left: 10px solid white;
}

.road::after {
    content: "";
    position: absolute;
    width: 10px;
    height: 100%;
    background-color: #282828;
    right: -65%;
    border-radius: 3px;
    animation: roadAnimation 1.4s linear infinite;
    border-left: 4px solid white;
}

.lampPost {
    position: absolute;
    bottom: 0;
    right: -90%;
    height: 90px;
    animation: roadAnimation 1.4s linear infinite;
}

@keyframes roadAnimation {
    0% {
        transform: translateX(0px);
    }

    100% {
        transform: translateX(-350px);
    }
}
</style>
