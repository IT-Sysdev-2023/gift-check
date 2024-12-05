<template>
    <a-card>
        <a-tabs>
            <a-tab-pane key="1">
                <template #tab>
                    <span style="font-weight: bold;">
                        Duplicated Report (CEBU)
                    </span>
                </template>
                <a-card style="width: 72%; margin-left: 28%;">
                    <a-tabs>
                        <a-tab-pane key="1">
                            <template #tab>
                                <span style="font-weight: bold;">
                                    <DashOutlined />
                                    Tagbilaran
                                </span>
                            </template>
                            <a-card>
                                <div style="margin-left: 60%;">

                                    <a-input-search allow-clear v-model:value="cebuTagbilaranSearch" enter-button
                                        placeholder="Input search here! " style="width: 100%;  " />
                                </div>
                                <div style=" margin-top: 10px;">
                                    <span style="font-weight: bold; margin-left: 40%;">
                                        Table Showing Tagbilaran
                                    </span>
                                </div>
                                <a-table :columns="cebuTable" :data-source="cebu.tagbilaran.data" :pagination="false"
                                    size="small" style="margin-top: 5px;">

                                </a-table>
                                <pagination :datarecords="cebu.tagbilaran" class="mt-5" />
                            </a-card>
                            <span>
                                <a-button @click="cebuGenerateExcel"
                                    style="margin-left: 77%; background-color: green; color:white;">
                                    <FileExcelOutlined />

                                    Generate Excel
                                </a-button>
                            </span>
                        </a-tab-pane>
                        <a-tab-pane key="2">
                            <template #tab>
                                <span style="font-weight: bold">
                                    <DashOutlined />
                                    Talibon
                                </span>
                            </template>
                            <a-card>
                                <div style="margin-left: 60%;">

                                    <a-input-search allow-clear v-model:value="cebuTalibonSearch" enter-button
                                        placeholder="Input search here! " style="width: 100%;  " />
                                </div>
                                <div style=" margin-top: 10px;">
                                    <span style="font-weight: bold; margin-left: 40%;">
                                        Table Showing Talibon
                                    </span>
                                </div>
                                <a-table :columns="cebuTable" :data-source="cebu.talibon.data" :pagination="false"
                                    size="small" style="margin-top: 5px;">

                                </a-table>
                                <pagination :datarecords="cebu.talibon" class="mt-5" />
                            </a-card>
                            <span>
                                <a-button @click="cebuGenerateExcel"
                                    style="margin-left: 77%; background-color: green; color:white;">
                                    <FileExcelOutlined />
                                    Generate Excel
                                </a-button>
                            </span>
                        </a-tab-pane>
                        <a-tab-pane key="3">
                            <template #tab>
                                <span style="font-weight: bold;">
                                    <DashOutlined />
                                    Tubigon
                                </span>
                            </template>
                            <a-card>
                                <div style="margin-left: 60%;">

                                    <a-input-search allow-clear v-model:value="cebuTubigonSearch" enter-button
                                        placeholder="Input search here! " style="width: 100%;  " />
                                </div>
                                <div style=" margin-top: 10px;">
                                    <span style="font-weight: bold; margin-left: 40%;">
                                        Table Showing Tubigon
                                    </span>
                                </div>
                                <a-table :columns="cebuTable" :data-source="cebu.tubigon.data" :pagination="false"
                                    size="small" style="margin-top: 5px;">

                                </a-table>
                                <pagination :datarecords="cebu.tubigon" class="mt-5" />
                            </a-card>
                            <span>
                                <a-button @click="cebuGenerateExcel"
                                    style="margin-left: 77%; background-color: green; color:white;">
                                    <FileExcelOutlined />
                                    Generate Excel
                                </a-button>
                            </span>
                        </a-tab-pane>
                    </a-tabs>
                </a-card>
                <a-card style="width: 25%; position: absolute; top:0;">
                    <div style="margin-left: 50px;">
                        <span>
                            <TagsOutlined />
                        </span>
                        <span style="font-weight: bold; font-size: 15px;">
                            Duplicate Barcodes
                        </span>
                        <div style="margin-left: 50px; color:#1e90ff; font-weight: bold;">
                            CEBU
                        </div>
                    </div>
                    <div style="margin-top: 20px;">
                        <span :style="{ fontSize: iconSize + 'px' }">
                            <FileSearchOutlined />
                        </span>

                        <a-input type="file" id="barcodeFile" @change="handleFileChange"
                            style="border: 2px solid #1e90ff; font-weight: bold; color:white; font-style: oblique; width: 117px;" />
                    </div>
                    <div style="margin-top: 10px;">
                        <span v-if="textfile && !cebu.selectedCebuFile" style="color:green; font-weight: bold;">Selected
                            TextFile: </span>
                        <span style="margin-left: 5px; text-decoration: underline;">
                            {{ this.textfile }}
                        </span>
                        <span v-if="cebu.selectedCebuFile" style="font-weight: bold; color:green">Selected
                            TextFile:</span>
                        <span style="margin-left: 5px; text-decoration: underline;">
                            {{ this.cebu.selectedCebuFile }}
                        </span>
                        <span v-if="!textfile && !cebu.selectedCebuFile" style="font-weight: bold;">
                            Note:
                        </span>
                        <span v-if="!textfile && !cebu.selectedCebuFile"
                            style="margin-left: 5px; color:red; font-weight: bold;">

                            Choose TEXTFILE type only !
                        </span>
                    </div>
                    <div>
                        <a-button @click="cebuReportButton"
                            style="background-color: #1e90ff; color: white; margin-top: 20px;">
                            <FileExcelOutlined />
                            Check Duplicates
                        </a-button>
                    </div>
                    <!-- {{ this.cebu.cebu }} -->
                </a-card>
                <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                <!-- {{ barcodes}} -->
            </a-tab-pane>
            <a-tab-pane key="2">
                <template #tab>
                    <span style="font-weight: bold;">
                        Duplicated Report (ALTTA CITTA)
                    </span>
                </template>

                <a-card style="width: 72%; margin-left: 28%;">
                    <a-tabs>
                        <a-tab-pane key="1">
                            <template #tab>
                                <span style="font-weight: bold;">
                                    <DashOutlined />
                                    Tagbilaran
                                </span>
                            </template>
                            <a-card>
                                <div style="margin-left: 60%;">

                                    <a-input-search allow-clear v-model:value="alttaTagbilaranSearch" enter-button
                                        placeholder="Input search here! " style="width: 100%;  " />
                                </div>
                                <div style=" margin-top: 10px">
                                    <span style="font-weight: bold; margin-left: 40%;">
                                        Table Showing Tagbilaran
                                    </span>
                                </div>
                                <a-table :columns="cebuTable" :data-source="altta.tagbilaran.data" :pagination="false"
                                    size="small" style="margin-top: 5px;">

                                </a-table>
                                <pagination :datarecords="altta.tagbilaran" class="mt-5" />
                            </a-card>
                            <span>
                                <a-button @click="alttaGenerateExcel"
                                    style="margin-left: 77%; background-color: green; color:white;">
                                    <FileExcelOutlined />
                                    Generate Excel
                                </a-button>
                            </span>

                        </a-tab-pane>
                        <a-tab-pane key="2">
                            <template #tab>
                                <span style="font-weight: bold;">
                                    <DashOutlined />
                                    Talibon
                                </span>
                            </template>
                            <a-card>
                                <div style="margin-left: 60%;">

                                    <a-input-search allow-clear v-model:value="alttaTalibonSearch" enter-button
                                        placeholder="Input search here! " style="width: 100%;  " />
                                </div>
                                <div style=" margin-top: 10px;">
                                    <span style="font-weight: bold; margin-left: 40%;">
                                        Table Showing Talibon
                                    </span>
                                </div>
                                <a-table :columns="cebuTable" :data-source="altta.talibon.data" :pagination="false"
                                    size="small" style="margin-top: 5px;">

                                </a-table>
                                <pagination :datarecords="altta.talibon" class="mt-5" />
                            </a-card>
                            <span>
                                <a-button @click="alttaGenerateExcel"
                                    style="margin-left: 77%; background-color: green; color:white;">
                                    <FileExcelOutlined />
                                    Generate Excel
                                </a-button>
                            </span>

                        </a-tab-pane>
                        <a-tab-pane key="3">
                            <template #tab>
                                <span style="font-weight: bold;">
                                    <DashOutlined />
                                    Tubigon
                                </span>
                            </template>
                            <a-card>
                                <div style="margin-left: 60%;">

                                    <a-input-search allow-clear v-model:value="alttaTubigonSearch" enter-button
                                        placeholder="Input search here! " style="width: 100%;  " />
                                </div>
                                <div style=" margin-top: 10px">
                                    <span style="font-weight: bold; margin-left: 40%;">
                                        Table Showing Tubigon
                                    </span>
                                </div>
                                <a-table :columns="cebuTable" :data-source="altta.tubigon.data" :pagination="false"
                                    size="small" style="margin-top: 5px;">

                                </a-table>
                                <pagination :datarecords="altta.tubigon" class="mt-5" />
                            </a-card>
                            <span>
                                <a-button @click="alttaGenerateExcel"
                                    style="margin-left: 77%; background-color: green; color:white;">
                                    <FileExcelOutlined />
                                    Generate Excel
                                </a-button>
                            </span>

                        </a-tab-pane>
                    </a-tabs>
                </a-card>

                <a-card style="width:25%; position: absolute; top:0;">
                    <div style="margin-left: 50px;">
                        <span>
                            <TagsOutlined />
                        </span>
                        <span style="font-weight: bold; font-size: 15px;">
                            Duplicate Barcodes
                        </span>
                        <div style="margin-left: 45px; color:#1e90ff; font-weight: bold;">
                            ALTTA CITTA
                        </div>
                    </div>
                    <div style="margin-top: 20px;">
                        <span :style="{ fontSize: iconSize + 'px' }">
                            <FileSearchOutlined />
                        </span>

                        <a-input type="file" id="barcodeFile" @change="handleFileChangeAltta"
                            enctype="multipart/form-data"
                            style="border: 2px solid #1e90ff; font-weight: bold; color:white; font-style: oblique; width: 117px;" />
                    </div>
                    <div style="margin-top: 10px;">
                        <span v-if="altaTextFile" style="color:green; font-weight: bold;">Selected TextFile: </span>
                        <span style="margin-left: 5px; text-decoration: underline;">
                            {{ this.altaTextFile }}
                        </span>

                        <span v-if="altta.selectedFile" style="color:green; font-weight: bold;">Selected
                            TextFile:</span>
                        <span style="margin-left: 5px; text-decoration: underline;">
                            {{ this.altta.selectedFile }}
                        </span>

                        <span v-if="!altaTextFile && !altta.selectedFile" style="font-weight: bold;">
                            Note:
                        </span>
                        <span v-if="!altaTextFile && !altta.selectedFile"
                            style="margin-left: 5px; color:red; font-weight: bold">
                            Choose CSV type only !
                        </span>
                    </div>
                    <div>
                        <a-button @click="alttaReportButton"
                            style="background-color: #1e90ff; color: white; margin-top: 20px;">
                            <FileExcelOutlined />
                            Check Duplicates
                        </a-button>
                    </div>
                    <!-- {{ this.altta.alttaTable }} -->

                </a-card>

            </a-tab-pane>
        </a-tabs>
    </a-card>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { TagsOutlined, FileSearchOutlined, CheckOutlined, PlayCircleFilled } from '@ant-design/icons-vue';
import { notification } from 'ant-design-vue';
import Description from '../Treasury/Description.vue';
import { placements } from 'ant-design-vue/es/vc-tour/placements';
import { message, Modal } from 'ant-design-vue';
import ColumnGroup from 'ant-design-vue/es/vc-table/sugar/ColumnGroup';
import Pagination from '@/Components/Pagination.vue';
import { mapState } from 'pinia';


export default {
    components: { Pagination },
    layout: AuthenticatedLayout,
    props: {
        altta: Object,
        cebu: Object,
    },
    data() {
        return {
            cebuTagbilaranSearch: this.cebu.tagbilaranSearch,
            cebuTalibonSearch: this.cebu.talibonSearch,
            cebuTubigonSearch: this.cebu.tubigonSearch,

            alttaTagbilaranSearch: this.altta.alttaTagbilaran,
            alttaTalibonSearch: this.altta.talibonSearch,
            alttaTubigonSearch: this.altta.tubigonSearch,

            cebuFileName: '',
            iconSize: 80,
            fileContent: '',
            barcodes: [],
            textfile: '',

            altaTextFile: '',
            altaContent: '',
            altaBarcode: [],
            alttaTable: [
                {
                    title: 'Transno',
                    dataIndex: 'transno'
                },
                {
                    title: 'Name',
                    dataIndex: 'name'
                },
                {
                    title: 'Barcode',
                    dataIndex: 'barcode'
                },
                {
                    title: 'Terminal',
                    dataIndex: 'terminal'
                },
                {
                    title: 'Business Unit',
                    dataIndex: 'bu'
                },
                {
                    title: 'Amount',
                    dataIndex: 'amount'
                },
                {
                    title: 'Verdate',
                    dataIndex: 'verdate'
                },
                {
                    title: 'Vertime',
                    dataIndex: 'vertime'
                },
                {
                    title: 'Store',
                    dataIndex: 'store'
                },

            ],
            cebuTable: [
                {
                    title: 'Transno',
                    dataIndex: 'transno'
                },
                {
                    title: 'Name',
                    dataIndex: 'name'
                },
                {
                    title: 'Barcode',
                    dataIndex: 'barcode'
                },
                {
                    title: 'Terminal',
                    dataIndex: 'terminal'
                },
                {
                    title: 'Business Unit',
                    dataIndex: 'bu'
                },
                {
                    title: 'Amount',
                    dataIndex: 'amount'
                },
                {
                    title: 'Verdate',
                    dataIndex: 'verdate'
                },
                {
                    title: 'Vertime',
                    dataIndex: 'vertime'
                },
                {
                    title: 'Store',
                    dataIndex: 'store'
                },
            ]
        };
    },
    watch: {
        cebuTagbilaranSearch(search) {
            console.log(search);
            const data = {
                search1: search,
                barcodes: this.cebu.cebu

            }
            this.$inertia.get(route('storeaccounting.DuplicatedBarcodes', data), {

            }, {
                preserveState: true
            })
        },
        cebuTalibonSearch(search) {
            console.log(search);
            const data1 = {
                search2: search,
                barcodes: this.cebu.cebu
            }
            this.$inertia.get(route('storeaccounting.DuplicatedBarcodes', data1), {

            }, {
                preserveState: true
            })
        },
        cebuTubigonSearch(search) {
            console.log(search);
            const data2 = {
                search3: search,
                barcodes: this.cebu.cebu
            }
            this.$inertia.get(route('storeaccounting.DuplicatedBarcodes', data2), {

            }, {
                preserveState: true
            })
        },
        alttaTagbilaranSearch(search) {
            console.log(search);
            // const data4 = {
            //     search4: search,
            //     // alttaBarcodeSearch: this.altta.alttaTable,
            //     // alttaBarcode: this.altta.alttaBarcode
            // }
            this.$inertia.get(route('storeaccounting.DuplicatedBarcodes'), {
                search: search,
                AlttaData: this.altta.alttaTable

            }, {
                preserveState: true
            })
        },
        alttaTalibonSearch(search) {
            console.log(search);
            this.$inertia.get(route('storeaccounting.DuplicatedBarcodes'), {
                search5: search,
                AlttaData: this.altta.alttaTable
            }, {
                preserveState: true
            })
        },
        alttaTubigonSearch(search) {
            console.log(search);
            this.$inertia.get(route('storeaccounting.DuplicatedBarcodes'), {
                search6: search,
                AlttaData: this.altta.alttaTable
            }, {
                preserveState: true
            })
        }
    },
    methods: {
        alttaGenerateExcel() {
            const data = this.altta.alttaTable
            if (this.altta.alttaTable === null) {
                const notificationWarning = (type) => {
                    notification[type]({
                        message: 'File Selection Required',
                        description: 'Please choose file first before generating EXCEL',
                        placement: 'topRight'
                    });
                };
                notificationWarning('warning');
                return;
            }
            Modal.confirm({
                title: 'Confirmation',
                content: 'Are you sure you want generate EXCEL?',
                okText: 'Yes',
                cancelText: 'No',
                onOk: () => {
                    window.location.href = route('storeaccounting.barcodes', { barcodes: data })
                },
                onCancel: () => {
                    console.log('Cancel');
                }

            });
        },

        handleFileChangeAltta(event) {
            const file = event.target.files[0];
            this.altaTextFile = file.name

            if (file) {
                const reader = new FileReader();

                reader.onload = (e) => {
                    this.altaContent = e.target.result;
                    console.log(this.altaContent);

                    this.altaBarcode = this.extractBarcodesAltta(this.altaContent);
                    console.log(this.altaBarcode);
                };
                reader.readAsText(file);
            }
        },
        extractBarcodesAltta() {
            const regex = /\b\d{1,20}\b/g;
            const barcodes = content.match(regex);
            return barcodes ? barcodes.map(barcode => barcode.trim().replace(/\t/g, '')) : [];
        },
        alttaReportButton() {
            if (this.altaContent) {
                const altaBarcodeString = this.altaContent
                const selectedFile = this.altaTextFile

                const openNotificationWithIcon = (type) => {
                    notification[type]({
                        message: 'Invalid File Selected',
                        description: 'Please select CSV type only',
                        placement: 'topRight'
                    });
                };
                const validTextFileAltta = ['csv'];
                const fileExtention = this.altaTextFile.split('.').pop().toLowerCase();

                if (!validTextFileAltta.includes(fileExtention)) {
                    openNotificationWithIcon('warning');
                    return;

                }
                Modal.confirm({
                    title: 'Confirmation',
                    content: 'Are you sure you want to check DUPLICATE BARCODES?',
                    okText: 'Yes',
                    cancelText: 'No',
                    onOk: () => {
                        window.location.href = route('storeaccounting.DuplicatedBarcodes', { AlttaData: altaBarcodeString, selectedFile });
                    },
                    onCancel: () => {
                        console.log('Cancel');
                    }
                });

            }
            else {
                const notificationWithIcon = (type) => {
                    notification[type]({
                        message: 'File Selection Required',
                        description: 'Please choose file first or the selected Textfile has no data found',
                        placement: 'topRight'
                    });
                };
                notificationWithIcon('warning');
                return;
            }

        },

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        cebuGenerateExcel() {
            if (this.cebu.cebu === null) {
                const notificationWithIcon = (type) => {
                    notification[type]({
                        message: 'File Selection Required',
                        description: 'Please choose file first before generating EXCEL',
                        placement: 'topRight'
                    });

                };
                notificationWithIcon('warning');
                return;
            }
            const data = this.cebu.cebu
            Modal.confirm({
                title: 'Confirmation',
                content: 'Are you sure you want to generate Excel?',
                okText: 'Yes',
                cancelText: 'No',
                onOk: () => {
                    window.location.href = route('storeaccounting.barcodes', { barcodes: data });
                },
                onCancel() {
                    console.log('Cancel');
                },

            });

        },

        handleFileChange(event) {
            const file = event.target.files[0];
            // console.log(file.name);
            this.textfile = file.name
            if (file) {
                const reader = new FileReader();

                reader.onload = (e) => {
                    this.fileContent = e.target.result;
                    console.log(this.fileContent);


                    this.barcodes = this.extractBarcodes(this.fileContent);
                    console.log(this.barcodes);
                };

                reader.readAsText(file);
            }


        },

        extractBarcodes(content) {
            const regex = /\b\d{1,20}\b/g;
            const barcodes = content.match(regex);
            return barcodes ? barcodes.map(barcode => barcode.trim().replace(/\t/g, '')) : [];
        },

        cebuReportButton() {
            if (this.fileContent) {
                const barcodeString = this.fileContent
                const selectedCebuFile = this.textfile

                const openNotificationWithIcon = (type) => {
                    notification[type]({
                        message: 'Invalid File Selected',
                        description: 'Please select Textfile type only',
                        placement: 'topRight',
                    });
                };
                const validFileExtention = ['txt', 'csv', 'tsv', 'log', 'json', 'xml', 'html', 'markdown', 'rtf'];
                const fileExtention = this.textfile.split('.').pop().toLowerCase();

                if (!validFileExtention.includes(fileExtention)) {
                    openNotificationWithIcon('warning');
                    return;
                }

                Modal.confirm({
                    title: 'Confirmation',
                    content: 'Are you sure you want to check DUPLICATE BARCODES?',
                    okText: 'Yes',
                    cancelText: 'No',
                    onOk: () => {
                        window.location.href = route('storeaccounting.DuplicatedBarcodes', { barcodes: barcodeString, selectedCebuFile });
                    },
                    onCancel: () => {
                        console.log('Cancel');
                    }
                });

            }
            else {
                const notificationWithIcon = (type) => {
                    notification[type]({
                        message: 'File Selection Required',
                        description: 'Please choose file first or the selected Textfile has no data found',
                        placement: 'topRight'
                    });
                };
                notificationWithIcon('warning');
                return;
            }
        }
    }


};
</script>
<style scoped>
@keyframes wave {

    0%,
    100% {
        color: black;
        font-weight: normal;
    }

    50% {
        color: red;
        font-weight: bold;
    }
}

.wave-animation {
    animation: wave 1s infinite;
}
</style>
