<template>
         <div class="flex justify-center gap-4 w-full max-w-5xl">
            <div class="border border-gray-300 rounded-lg p-4 shadow-md bg-white  w-1/2">
                
                <!-- Approved Budget Request Info -->
                <a-descriptions :column="1"title="Approved Budget Request Info" bordered>
                <a-descriptions-item label="BR No.":span="1">{{ data.br_no}}</a-descriptions-item>
                <a-descriptions-item label="Date Requested">{{data.br_requested_at }}</a-descriptions-item>
                  <!-- <a-descriptions-item label="Time Requested"class="w-1/2">{{ }}</a-descriptions-item> -->
                <a-descriptions-item label="Budget Requested" >{{ data.br_request }}</a-descriptions-item>
                <a-descriptions-item label="Request Document" v-if="data.br_file_docno">
                  <a-image  style="height: 100px" :src="data.br_file_docno" :fallback="imageUrl"/>
            </a-descriptions-item >
               
                 <a-descriptions-item label="Request Remark">{{ data.br_remarks}}</a-descriptions-item>
                 <a-descriptions-item label="Prepared By">{{ data.prepared_by.full_name }}</a-descriptions-item>
              </a-descriptions>
         </div>
    
                 <!-- Approved Budget Details -->
            <div class="border border-gray-300 rounded-lg p-4 shadow-md bg-white w-1/2">
                 <a-descriptions bordered :column="1" title="Approved Budget Details" > 
                 <!-- <a-descriptions bordered style="margin-top: 20px" :column="1"> -->
                 <a-descriptions-item label="Date Approved">{{data.abr?.approved_at }}</a-descriptions-item>
                 <a-descriptions-item label="Approved Document" v-if="data.abr?.file_doc_no">
                 <a-image style="height: 100px" :src="data.abr?.file_doc_no" :fallback="imageUrl" />
         </a-descriptions-item>
     
                 <a-descriptions-item label="Approved Remarks"> <a-tag color="cyan">{{ }}</a-tag></a-descriptions-item>
                 <a-descriptions-item label="Approved By" :span="2">{{  data.abr?.approved_by }}</a-descriptions-item>
                 <a-descriptions-item label="Checked By">{{ data.abr?.checked_by}}</a-descriptions-item>
                 <a-descriptions-item label="Prepared By">{{data.abr?.user_prepared_by.full_name }}</a-descriptions-item>
             </a-descriptions>
          </div>
     </div>


        <!-- Print Button| Norien -->
        <button 
            @click="printPage" 
            class="mt-5 px-20 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700"> Print </button>

        <!-- Printable Section -->
        <div id="print-section" class="hidden print-area">
            <div class="p-10 border border-gray-500 shadow-md bg-white text-black w-[8.5in] h-[11in] mx-auto">
                <h2 class="text-center text-xl font-bold mb-4">GIFT CHECK MONITORING SYSTEM</h2>
                <h3 class="text-center text-lg font-bold mb-6">REVOLVING BUDGET ENTRY FORM</h3>

                <!-- Print Table -->
                <table class="w-full border-collapse border border-black text-left">
                    <tr>
                        <th class="border border-black px-2 py-1 w-1/3">BR. No.</th>
                        <td class="border border-black px-2 py-1">{{ data.br_no }}</td>
                    </tr>
                    <tr>
                        <th class="border border-black px-2 py-1">Date Requested:</th>
                        <td class="border border-black px-2 py-1">{{ data.br_requested_at }}</td>
                    </tr>
                    <tr>
                        <th class="border border-black px-2 py-1">Budget:</th>
                        <td class="border border-black px-2 py-1">₱{{ data.br_request }}</td>
                    </tr>
                    <tr>
                        <th class="border border-black px-2 py-1">Remarks:</th>
                        <td class="border border-black px-2 py-1">{{ data.br_remarks }}</td>
                    </tr>
                </table>

                <!-- Signatures -->
                <div class="grid grid-cols-2 text-center mt-8">
                    <div>
                        <p class="font-bold underline">{{ data.prepared_by.full_name }}</p>
                        <p>Prepared By</p>
                    </div>
                    <div>
                        <p class="font-bold underline">{{ data.abr?.checked_by }}</p>
                        <p>Checked By</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 text-center mt-8">
                    <div>
                        <p class="font-bold underline">{{ data.abr?.reviewed_by }}</p>
                        <p>Reviewed By</p>
                    </div>
                    <div>
                        <p class="font-bold underline">{{ data.abr?.approved_by }}</p>
                        <p>Approved By</p>
                    </div>
                </div>

                <!-- Company Footer -->
                <div class="text-center mt-12 text-sm margin">
                    <p>Alturas Group of Companies</p>
                    <p>Dampas Dist. Tagbilaran City, Bohol, Philippines</p>
                    <p>Tel#: (038) 501-6284 Operator: (038) 501-3000 Local: 1953/1844</p>
                </div>
            </div>
        </div>
         <!-- END| Norien-->

</template>

<script>
import { imageFallback } from "@/Mixin/UiUtilities";
export default {
    props: {
        data: Object,
    },
    data() {
        return {
            imageUrl: imageFallback(), // Placeholder for the image URL
        };
    },
    methods: {
        printPage() {
            const printContent = document.getElementById("print-section").innerHTML;
            const originalContent = document.body.innerHTML;
            document.body.innerHTML = printContent;
            window.print();
            document.body.innerHTML = originalContent;
            window.location.reload();
        },
    },
};
</script>
    <style scoped>
            /* Print Styling */
            @media print {
                @page {
                size: A4 portrait; /* or 'letter portrait' */
                margin: 1in; /* 1-inch margin for proper formatting */
           }

             .print-container {
              width: 8.5in; /* Letter Size Width */
              height: 11in; /* Letter Size Height */
              padding: 1in; /* 1-inch margin */
              page-break-after: always;
           }
             /* Hide print button */
             button {
             display: none;
           }
       }
        
            
           /* Adjust table for printing */
             table {
              width: 100%;
              font-size: 12pt;
              border: 1px solid black;
        }

            th, td {
             padding: 8px;
             border: 1px solid black;
     }
</style>
