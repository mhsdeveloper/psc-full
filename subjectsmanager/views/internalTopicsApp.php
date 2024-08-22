<?php include("head.php"); ?>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coop - Topics Manager</title>
    <link rel="stylesheet" href="./style/css/main.css?v=<?=$FRONTEND_VERSION;?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<div v-if="isEditor" id="internalTopicsApp">
    <div class="modal" v-show="isModalOpen">
        <div class="modal-content">
            <span class="close" @click="closeModal">&times;</span>
            <div>
                <h2><i class="fa fa-edit"></i> Edit Topics</h2>
                <div class="d-flex-centered search-small">
                    <input class="search-small border" placeholder="Search For Topic To Edit" v-model="editSearchString" type="text">
                    <button class="search-button"><i class="fa fa-search"></i></button>
                </div>
                <div class="search-small-display">
                    <p v-for="result in editSearchResults" class="searchRes" @click="setEditTopic(result.topic_name, false)">{{result.topic_name}}</p>
                </div>
                <hr>
                <div class="editTopic mt-2" v-if="editTopic.length != 0">
                    <div class="mt-2">
                        <h3>Topic Name</h3>
                        <input id="editTopicName"type="text" v-model="editTopic">
                    </div>

                    <div>
                        <div class="flex">
                            <h3>See Value?</h3>                    
                            <input id="editUmbrella" v-model="editHasSee" type="checkbox">
                        </div>
                        
                        <p class="italics gray-text">Select if a topic is not directly supported, but has a see instead value</p>
        
                    </div>
                                   
                    <div v-if="editHasSee == true">
                        <label for="seeSelect">Select See: </label>
                        <select id="seeSelect" v-model="editSee" required>
                            <option v-for="option in allOptions">{{option.topic_name}}</option>
                        </select>
                    </div>
                    <div v-else>
                        <div class="flex mt-1">
                            <h3 for="editUmbrella">Umbrella Term?</h3>
                            <input id="editUmbrella" v-model="editIsUmbrella" type="checkbox">
                        </div>
                        <p class="italics gray-text">Select if a topic has subtopics that refer to it</p>
                        <h3>Consensus Definition</h3>
                        <p class="italics gray-text m-0">A definition of the topic that is shared by all topics (50 characters max)</p>
                        <textarea id="editConsensusDef" v-model="editConsensusDef" maxlength="50"></textarea>
                    
                        
                        <div v-if="editIsUmbrella == false">
                            <div v-if="editUmbrellaTerms.length > 0">
                                <h3>Current Umbrella Terms(s) for this Topic</h3>
                                <ul>
                                    <li class="umb flex" v-for="umb in editUmbrellaTerms">
                                        <p>- {{umb}}</p>
                                        <p class="delete px-1"  @click="deleteEditUmb(umb)"> delete </p>
                                    </li>
                                </ul>
                            </div>
                            <div class="left-list-padding">
                                <h3 class="m0 mt-1"> Add Umbrella Topic</h3>
                                <label for="umbrellaSelect" class="italics gray-text">Select Umbrella Term to Add: </label>
                                <select id="umbrellaSelect" v-model="editSelectedUmbrellaTerm" @change="addUmbrella">
                                    <option v-for="option in allUmbrellas">{{option.topic_name}}</option>
                                </select>
                            </div>
                           
                            <!-- <input class="search-small border" placeholder="Search For Topic To Edit" v-model="editUmbSearchString">
                            <p v-for="result in editUmbSearchResults" @click="setEditUmb(result.topic_name)" class="searchRes">{{result.topic_name}}</option> -->
                        </div>
                    </div>

                   
                    
                    <br><br>
                    <button class="save" @click="saveEdit">Save Changes</button>
                </div>
                <div v-if="editSearchString.length != 0 && editSearchResults == 0 && editTopic.length == 0">
                    <p>{{editSearchString}} is not a current topic. <span class="add" @click="setEditTopic(editSearchString, true)">Create topic?</span></p>
                </div>
            </div>
        </div>
    </div>
    <div class="p-2 title">
        <div class="d-flex space-between">
            <h2 class="m-0">Selected Project: {{selectedProj}}</h2>
            <button id="editTopicsButton" v-if="isSuperAdmin" @click="openModal"><i class="fa fa-edit"> </i>Edit Topics</button>
        </div>

        
        <div class="projectTopicDisplay" id="projectTopicDisplay">
            <p>Recently Edited Topics:</p><a v-for="topic in recentlyEditedTopics" @click="setTopic(topic.topic_name)">{{topic.topic_name}}</a> 
        </div>
        <!-- <p @click="unclampProjTopicDisplay()">(Expand)</p> -->
        
    </div>

    <div class="main-container">
        <div class="sidebar">
            <h3 class="px-1">Search Topics</h3>
            <div class="d-flex-centered search-small">
                <input class="search-small" v-model="searchString" type="text" placeholder="Type * To View All Topics">
                <button class="search-button" @click="search()"><i class="fa fa-search"></i></button>
            </div>
            <div class="search-small-display">
                <p v-for="result in searchResults" class="searchRes" @click="setTopic(result.topic_name)">{{result.topic_name}}</p>
            </div>
            <!-- <div class="px-1 d-flex">
                <input type="checkbox">
                <p class="text-small">Show Only Currently Assigned Topics</p>
            </div> -->
            
            <hr>
            <h3 class="px-1">Browse All Topics</h3>
            <div class="accordion-container">
                <div  v-for="umbrella in allUmbrellas">
                    <button class="accordion termtitle" @click="fetchSubTopics(umbrella.topic_name, $event)">{{umbrella.topic_name}}</button>
                    <div class="panel" v-if="currUmbrella === umbrella.topic_name">
                        <ul>
                            <li v-for="subtopic in currAccordian" @click="setTopic(subtopic.topic_name)"> {{subtopic.topic_name}}</li>
                        </ul>
                    </div>
                    
                </div>
                </div>
        </div>

       
        <div v-if="currSubject.length > 0" class="content">
            <div class="px-1 italics" v-if="isSee == true">
                <h3>See {{seeTarget}}</h3>
                <hr>
            </div>
            <div class="d-flex space-between p-2 pt-3 ">
                <div>
                    <a @click="viewAll()">&#129044; View All Assigned Topics</a>
                    <h1 class="m-0">{{currSubject}} </h1>
                    <p v-if="currTopicData.consensusDefinition" class="consensus"> <b>Consensus Definition: </b> {{currTopicData.consensusDefinition}} </p>
                </div>
                
                <!-- <div v-if="projTopicData.find(({ topic_name }) => topic_names === currSubject)" class="button"><i class="fa fa-plus"></i> Edit to add Topic to Project </div> -->
                <div v-if="projTopicData.find(e => e.topic_name == currSubject)" class="red-bg button" @click="removeTopic">
                    <i class="fa fa-minus"></i>  Remove From Project
                </div>
                <div v-if="!projTopicData.find(e => e.topic_name == currSubject)" class="light-blue-bg button" @click="addTopic">
                    <i class="fa fa-plus"></i> Add Topic to Project 
                </div>
            </div>
            <hr class="my-1"/>
            <div class="px-1">
                <h2 class="">Related Terms</h2>
                <ul>
                    <li v-if="broader.length > 0">
                        <h3>Broader Terms:</h3>
                        <ul>
                            <li class="term" v-for="term in broader" @click="setTopic(term.topic_name)">{{term.topic_name}}</li>
                        </ul>
                    </li>
                    <li v-if="narrower.length > 0">
                        <h3>Narrower Terms:</h3>
                        <ul>
                            <li class="term" v-for="term in narrower" @click="setTopic(term.topic_name)">{{term.topic_name}}</li>
                        </ul>
                    </li>
                    <!-- <li>
                        <h3>See Also:</h3>
                        <ul id="seeAlso"></ul>
                    </li> -->
                </ul>
            </div>
    
            <div class="p-1">
                <h2 class="">{{selectedProj}}'s Notes For This Topic</h2>
            </div>
            <div class="projects-container">
                <div class="project flex">
                    <div class="w-100">
                        <div class="d-flex space-between">
                            <h3>Private Note</h3>
                            <div v-if="!internalNoteIsEditing" @click="internalNoteToggleEdit"><i class="fa fa-edit"></i> Edit</div>
                            <div v-else @click="internalNoteSaveChanges"><i class="fa fa-save"></i> Save</div>
                        </div>

                        <div v-if="!internalNoteIsEditing">
                            <p v-if="internalNoteContent">{{ internalNoteContent }}</p>
                            <p v-else>Edit to add a Private Note</p>
                        </div>

                        <div v-else>
                            <textarea
                                v-model="internalNoteEditedContent"
                                :placeholder=editPlaceholder
                            ></textarea>
                        </div>
                    </div>
                </div>
                <div class="project flex">
                    <div class="w-100">
                        <div class="d-flex space-between">
                            <h3>Public Note</h3>
                            <div v-if="!publicNoteIsEditing" @click="publicNoteToggleEdit"><i class="fa fa-edit"></i> Edit</div>
                            <div v-else @click="publicNoteSaveChanges"><i class="fa fa-save"></i> Save</div>
                        </div>

                        <div v-if="!publicNoteIsEditing">
                            <p v-if="publicNoteContent">{{ publicNoteContent }}</p>
                            <p v-else>Edit to add a Public Note</p>
                        </div>

                        <div v-else>
                            <textarea
                                v-model="publicNoteEditedContent"
                                :placeholder=editPlaceholder
                            ></textarea>
                        </div>
                    </div>
                </div>
                </div>
            </div>
            <div v-else class="content p-2">
                <h1>{{selectedProj}}'s Currently Assigned Topics</h1>
                <div class="multi-col">
                    <div v-for="letter in Object.keys(groupedData)">
                        <h2>{{letter}}</h2>
                        <div class="topic" v-for="topic in groupedData[letter]" @click="setTopic(topic.topic_name)">
                            {{topic.topic_name}}
                        </div>
                    </div>
                </div>
           
            </div>
        </div>
        
    </div>


</div>

<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script type="module">

import { createApp } from 'https://unpkg.com/vue@3/dist/vue.esm-browser.js';
// import EditableNote from './EditableNote.vue';
import OApp from "/subjectsmanager/views/scripts/topicsDisplay.js?v=<?=$FRONTEND_VERSION;?>";

const App = createApp(OApp);
App.mount("#internalTopicsApp"); //mount at the id passed as first arg
</script>


</html>




