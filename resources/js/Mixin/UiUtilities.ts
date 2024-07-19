import { message } from 'ant-design-vue';
import { ref } from "vue";

export function highlighten() {
    const highlightText = (text: string, searchQuery: any) => {
        if (!searchQuery) return text;
        if (text != null) {
            const regex = new RegExp(searchQuery, "gi");
            return text
                .toString()
                .replace(
                    regex,
                    (match) =>
                        `<span style="background-color: yellow">${match}</span>`
                );
        }
    };

    return { highlightText };
}

export function onProgress(){
    const loadingMessageKey = 'loadingMessage';

    const onLoading = () => {
        message.loading({ content: 'Action in progress..', key: loadingMessageKey, duration: 0 });
    } 
    
    const notification = (page: {success: string, error: string}) => {
        if (page.success) {
            message.success({ content:  page.success, key: loadingMessageKey, duration: 3.5 });
        }
        
        if (page.error) {
            message.error({ content:  page.error, key: loadingMessageKey, duration: 3.5 });
        }
    }

    return {notification, onLoading};
}

export const onLoading = ref(false);

export function dashboardRoute() {
    const webRoute = route().current();
    const res = webRoute?.split(".")[0];
    return res + ".dashboard";
};

export function amountInWords(number)
{
    // convertNumberToWords(number) {
    //     const units = ['','one','two','three','four','five','six','seven','eight','nine'];
    //     const teens = ['','eleven','twelve','thirteen','fourteen','fifteen','sixteen','seventeen','eighteen','nineteen'];
    //     const tens = ['','ten','twenty','thirty','forty','fifty','sixty','seventy','eighty','ninety'];
    //     const thousands = ['','thousand','million','billion','trillion'];
  
    //     function convertToWords(num) {
    //       if (num === 0) return 'zero';
  
    //       let words = '';
  
    //       for (let i = 0; i < thousands.length; i++) {
    //         let tempNumber = num % (100 * Math.pow(1000, i));
    //         if (Math.floor(tempNumber / Math.pow(1000, i)) !== 0) {
    //           if (Math.floor(tempNumber / Math.pow(1000, i)) < 20) {
    //             words = units[Math.floor(tempNumber / Math.pow(1000, i))] + thousands[i] + ' ' + words;
    //           } else {
    //             words = tens[Math.floor(tempNumber / (10 * Math.pow(1000, i)))] + ' ' + units[Math.floor(tempNumber / Math.pow(1000, i)) % 10] + ' ' + thousands[i] + ' ' + words;
    //           }
    //         }
    //         num = num - tempNumber;
    //       }
  
    //       return words.trim();
    //     }
  
    //     return convertToWords(number);
    //   }
}
// export const onTableLoading = ref(false);
