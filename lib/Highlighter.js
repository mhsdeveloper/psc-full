"use strict";
class Highlighter {
    /**
     * @param {string} elementId the id of the target element
     * @param {*string} elementType the type(div, span, table, etc)
     * @param {*string} startHighlightTag the start of the desired highlight tag
     * @param {*string} endHighlightTag end of the highlight tag
     */
    constructor(elementId, elementType = "div", startHighlightTag = '<span class="highlight">', endHighlightTag = '</span>') {
        this.elementId = elementId;
        this.elementType = elementType;
        this.startHighlightTag = startHighlightTag;
        this.endHighlightTag = endHighlightTag;
        this.lenAddedByTags = this.startHighlightTag.length + this.endHighlightTag.length;
        this.targetElement = document.getElementById(this.elementId);
        if (!this.targetElement) {
            throw new Error("Unable to highlight: unable to identify target element");
        }
        this.walker = document.createTreeWalker(this.targetElement, NodeFilter.SHOW_TEXT, null, false);
        if (!this.walker) {
            throw new Error("Unable to create Tree Walker");
        }
        this.targetStartIndex = 0;
        this.currTargetIndex = 0;
        this.distance = 0;
    }

    reset(){
        this.walker = document.createTreeWalker(this.targetElement, NodeFilter.SHOW_TEXT, null, false);
        if (!this.walker) {
            throw new Error("Unable to create Tree Walker");
        }
        this.targetStartIndex = 0;
        this.currTargetIndex = 0;
        this.distance = 0;
    }

    /**
     *
     * @param {String}  targetPhrase The phrase that the highlighter will search for and highlight
     */
    highlight(targetPhrase) {

       this.#validateAndFormatTargetPhrase(targetPhrase);

        while (this.walker.nextNode()) {
            // if (!this.#validateCurrNode) continue;
        
            this.#setCurrNode();

            if (!this.#validateCurrNode()) continue;
            
            this.text = this.#formatNodeText();
            this.occurances = 0;

            for (let i = 0; i < this.text.length; i++) {
                //if the current ch of text being scanned matches with the ch at the current index of the  this.targetPhrase
                if (this.text[i] == this.targetPhrase[this.currTargetIndex]) {

                    this.#handleIfFirstLetterMatch(i);

                    //if the  this.targetPhrase index has matched to the extent that the  this.targetPhrase phrase is finished
                    if (this.currTargetIndex == this.targetPhrase.length - 1) {
                        
                        //if you've been inside of one element the entire time
                        if (this.distance == 0) {
                            
                            this.#handlePhraseMatchWithinOneElement(i);

                        }
                        //else you jumped from different elements
                        else {
                            this.#handlePhraseMatchAcrossElementsEnd(i)

                            //by this point, you are at the start of the original this.targetPhrase phrase
                            if (!this.temp) continue;

                            let text2 = this.temp.nodeValue;
                            if (!text2) continue;

                            this.#handlePhraseMatchAcrossElementsStart(text2)
                        }
                    }
                    else {
                        // console.log(text[i] +  this.targetPhrase[currTargetIndex])
                        this.currTargetIndex++;
                    }
                    //if the current text finishes before the current text is done, add one to the this.distance so that we can trace back to this node to find the begining
                    if (i == this.text.length - 1) {
                        this.distance += 1;
                    }
                    //if the current ch of text being scanned matches with the targetIndex but is offset by one (to allow buffer for spaces)
                }
                else if (this.text[i] == this.targetPhrase[this.currTargetIndex + 1]) {
                    this.currTargetIndex += 2;
                    if (i == this.text.length - 1) {
                        this.distance += 1;
                    }
                }
                else {
                    this.targetStartIndex = this.distance = this.currTargetIndex = 0;
                }
            }
        }
    }

    /**
     * Funciton to replace the text html additions with actual dom elements. 
     * This function must be called at the end of highlighting operations. 
     * If highlighting mutliple phrases, with multiple instances, you can just call this once at the end
     */
    apply() {
        const portion = document.getElementById(this.elementId);
        if (portion && portion instanceof HTMLElement) {
            let text = portion.innerHTML;
            text = text.replace(/&lt;/g, '<').replace(/&gt;/g, '>');
            const highlightedPortion = document.createElement(this.elementType);
            highlightedPortion.innerHTML = text;
            if (portion.parentElement && portion.parentElement instanceof HTMLElement) {
                portion.parentElement.replaceChild(highlightedPortion, portion);
            }
            else {
                throw new Error("Unable to highlight: target element must not be a top level child of the document. Try enclosing it within a div?");
            }
        }
        else {
            throw new Error("Unable to highlight: an element with that Id could not be found");
        }
    }

     //TODO: remove punctuation and increase offset to account for adding it back in
    #removePunctuation(text) {
        const regex = /[!"#$%&'()*+,-./:;=?@[\]^_`{|}~]/g;
        // return text.replace(regex, '');
        return text;
    }

    #validateAndFormatTargetPhrase(targetPhrase){
        this.targetPhrase = targetPhrase;
        if (!this.targetPhrase) {
            throw new Error("Unable to highlight: invalid target phrase inputted");
        }
        this.targetPhrase = this.#removePunctuation(this.targetPhrase.trim().toLowerCase());
    }

    #setCurrNode(){
        this.currNode = this.walker.currentNode;
        this.currNodeVal = this.currNode.nodeValue;
    }

    // if there next node doesnt have a value or is just whitespace, skip it. 
    // if you have started a  this.targetPhrase match, add one to the this.distance
    #validateCurrNode(){
        if (!this.currNodeVal || !/\S/.test(this.currNodeVal)) {
            if (this.distance != 0)
                this.distance++;
            return false
        } else {
            return true
        }
    }

    #formatNodeText(){
        return this.#removePunctuation(this.currNodeVal.toLowerCase());
    }

    #handleIfFirstLetterMatch(i){
        //if its the first character
        if (this.currTargetIndex == 0) {
           
            //store that index in the targetStart index so that we can reference it later to add the start tag to 
            this.targetStartIndex = i;
        }
    }

    #handlePhraseMatchWithinOneElement(i){
        this.currNodeVal = this.currNode.nodeValue;
        //if finding occurances of the multiple phrase, adjust for the word length added by other tags 
        let offset;
        if (this.occurances != 0) {
            offset = (this.occurances * this.lenAddedByTags) + 1;
            this.targetStartIndex += (this.occurances * this.lenAddedByTags);
        }
        //otherwise the word only occured once
        else {
            offset = 1;
        }

        //create "sentence" aka the target phrase wrapped in the highlight tags
        const sentence = this.currNodeVal.slice(0, this.targetStartIndex) + this.startHighlightTag + this.currNodeVal.slice(this.targetStartIndex, i + offset) + this.endHighlightTag + this.currNodeVal.slice(i + offset);
        this.currNode.nodeValue = sentence;
        this.occurances++;
    }
    
    //add highlight tags to the end of the target phrase and then "rewind" to the start of the target phrase
    #handlePhraseMatchAcrossElementsEnd(i){
        //add span from the start of the current element to the indicated end(i+1)
        const sentence = this.startHighlightTag + this.currNodeVal.slice(0, i + 1) + this.endHighlightTag + this.currNodeVal.slice(i + 1);
        this.currNode.nodeValue = sentence;
        this.temp;
        //then go to the previous node
        this.temp = this.walker.previousNode();
        //go back as many times as this.distance from the start
        for (let i = 0; i < this.distance - 1; i++) {
            //if those nodes are not all white space
            if (!this.temp) {
                continue;
            }
            this.text = this.temp.nodeValue;
            if (this.text && /\S/.test(this.text)) {
                //wrap them in a span
                const wrapped = this.startHighlightTag + this.text + this.endHighlightTag;
                this.temp.nodeValue = wrapped;
            }
            //get the previous node
            this.temp = this.walker.previousNode();
        }
    }

    #handlePhraseMatchAcrossElementsStart(text2){
        //add span from the end of the current element to the indicated start(targetStartIndex)
        //create "sentence" aka the target phrase wrapped in the highlight tags
        const sentence2 = text2.slice(0, this.targetStartIndex) + this.startHighlightTag + text2.slice(this.targetStartIndex) + this.endHighlightTag;
        
        this.temp.nodeValue = sentence2;
        
        //go back to where the closing tag was placed and continue forward
        for (let i = 0; i < this.distance - 1; i++) {
            this.currNode = this.walker.nextNode();
        }
    }

}
