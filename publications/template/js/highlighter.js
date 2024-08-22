/**
 * Potential Improvements: 
 * **Make highlighter punctuation insenstive 
*/

class Highlighter {
    constructor(elementId, elementType = "div"){
        this.elementId = elementId;
        this.elementType = elementType
    }

    highlight(targetPhrase) {
        targetPhrase = targetPhrase.trim().toLowerCase();
        const targetElement = document.getElementById(this.elementId)
        const walker = document.createTreeWalker(targetElement, NodeFilter.SHOW_TEXT, null, false);
        let targetStartIndex = 0;
        let currTargetIndex = 0;
        let distance = 0;
		let wrapperStart = '<span class="highlight">';
		let spanCharCount = wrapperStart.length + 7; //7 is count of "</span>"
		
        while(walker.nextNode() ) {
            let currNode = walker.currentNode;
            let nodeVal = currNode.nodeValue;

            //if there next node doesnt have a value or is just whitespace, skip it. If you have started a targetPhrase match, add one to the distance.
            if (!nodeVal || !/\S/.test(nodeVal)){
                if(distance != 0) distance++;
                continue
            }
    
            console.log(nodeVal);
            let text = nodeVal.toLowerCase();
            let occurances  = 0;
            for(let i=0; i < text.length; i++){
                //if the current ch of text being scanned matches with the ch at the current index of the targetPhrase
                if (text[i] == targetPhrase[currTargetIndex]){
                    //if its the first character
                    if (currTargetIndex == 0){
                        //store that index in the targetStart index so that we can reference it later to add the start tag to 
                        targetStartIndex = i;
                    }
                    //if the targetPhrase index has matched to the extent that the targetPhrase phrase is finished
                    if (currTargetIndex == targetPhrase.length-1){
                        //if you've been inside of one element the entire time
                        if (distance == 0){
                            nodeVal = currNode.nodeValue;
                            //if finding multiple words 
                            let offset;
							if(occurances != 0){
                                offset = (occurances *  spanCharCount) + 1;
                                targetStartIndex += (occurances * spanCharCount);
                            } else{
                                offset = 1;
                            }
                            
                            const sentence = nodeVal.slice(0, targetStartIndex) + wrapperStart + nodeVal.slice(targetStartIndex, i+offset) + '</span>' + nodeVal.slice(i+offset); 
                            currNode.nodeValue = sentence;  
                            occurances++;
                            
                        //if you jumped from different elements
                        } else {
                            //add span from the start of the current element to the indicated end(i+1)
                            const sentence = wrapperStart + nodeVal.slice(0, i+1) + '</span>' + nodeVal.slice(i+1); 
                            currNode.nodeValue = sentence;
                            let text2;
                            let temp;
    
                            //then go to the previous node
                            temp = walker.previousNode()
                            //go back as many times as distance from the start
                            for (let i = 0; i < distance-1; i++){
                                //if those nodes are not all white space
                                text = temp.nodeValue;
                                if (text && /\S/.test(text)){
                                    //wrap them in a span
                                    const wrapped = wrapperStart + text + '</span>';
                                    temp.nodeValue = wrapped;
                                }
                                //get the previous node
                                temp = walker.previousNode()
                            }
                            //by the end of the loop youre at the start of the targetPhrase phrase
                            text2 = temp.nodeValue
                            //add span from the end of the current element to the indicated start(targetStartIndex)
                            const sentence2 = text2.slice(0, targetStartIndex) + wrapperStart + text2.slice(targetStartIndex) + '</span>';
                            temp.nodeValue = sentence2;
                            
                            //go back to where the closing tag was placed and continue forward
                            for (let i = 0; i < distance-1; i++){
                                currNode = walker.nextNode();
                                console.log('going next!')
                            }
                        }
    
                    } else{
                        console.log(text[i] + targetPhrase[currTargetIndex])
                        currTargetIndex++ 
                    }
                    
                    //if the current text finishes before the current text is done, add one to the distance so that we can trace back to this node to find the begining
                    if(i == text.length-1){
                        distance += 1
                    }
    
                //if the current ch of text being scanned matches with the targetIndex but is offset by one (to allow buffer for spaces)
                } else if(text[i] == targetPhrase[currTargetIndex +1]){
    
                    currTargetIndex += 2;
    
                    if(i == text.length-1){
                        distance += 1
                    }
                    
                }else{
                    targetStartIndex = distance = currTargetIndex = 0;
                }
            }   
        }
    }

    apply(){
        const portion = document.getElementById(this.elementId);
        let text = portion.innerHTML;
        text = text.replace(/&lt;/g, '<').replace(/&gt;/g, '>');
        console.log(text);

        const highlightedPortion = document.createElement(this.elementType);
        highlightedPortion.innerHTML = text;
        portion.parentElement.replaceChild(highlightedPortion, portion);
    }
}