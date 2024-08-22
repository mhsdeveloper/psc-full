<?php


    /* the interface that all TxtToChunks implimentations should use
     * 
     */


    namespace Publications;
    
    
    
    interface ITxtToChunks {
        
        
        
        /* this should return true or false for success or failure
         * it is called by $this->each() and operates on $this->chunk
         */
        
        public function processChunk();
        
        
    }
    