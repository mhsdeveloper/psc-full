<?php ?>

		<div class="app" id="stepsapp">

			<q-btn label="Back to XML files" class="q-ma-sm" icon="keyboard_arrow_left" color="primary" onclick="location.href='/tools/docmanager/index.php'"></q-btn>
			
			<h1>Workflow Steps</h1>
			<h2>Manage the design of your workflow, because it's all about the flow of your work, i.e., the workflow</h2>
			
			<? // ERROR BAR ?>
			<div class="errorBar" v-if="errorMsgs.length">
				<span tabindex=0 class="closeButton" v-on:click="closeError" v-on:keypress="closeError">×</span>
				<p v-for="error in errorMsgs">{{error}}</p>
			</div>

			<p>Click a step to edit or delete</p>

			<section>
				<div class="Mrow">
					<div class="step" v-for="(step, index) in steps" v-bind:id="step.id" v-bind:data-step="index" v-on:click="edit" v-on:keypress="edit">
						<label v-bind:style="'border-left: 6px solid ' + step.color + ';'">{{step.order}}</label>
						<h2>{{step.short_name}} <span>( {{step.name}} ) </span></h2>
						<p>{{step.description}}</p>
					</div>
				</div>
			</section>
			<div class="Mrow">
				<q-btn color="primary" icon="add" label="Add Step"  tabindex="0" v-on:click="showAdd = true"></q-btn>
			</div>



			<q-dialog v-model="showEditor">
				<q-card>
					<q-card-section>
					<div class="text-h6">Edit Step</div>
					</q-card-section>

					<q-separator></q-separator>
					<template  v-model="stepEdits">
						<div class="Mform">

							<div class="Mrow">
								<q-input outlined v-model="stepEdits.name" label="Name of this step"></q-input>
								<p class="hint"></p>
							</div>
							<div class="Mrow">
								<q-input outlined v-model="stepEdits.short_name" label="Step label"></q-input>
								<div class="hint">This is how the step will be labeled in the list of documents.</div>
							</div>
							<div class="Mrow">
								<q-input outlined v-model="stepEdits.description" type="textarea" label="Description"></q-input>
								<div class="hint">Describe this step to aid in understanding what is involved in completing it, and also the document's state when this step is complete.</div>
							</div>
							<div class="Mrow">
								<q-input outlined v-model="stepEdits.order" type="number" label="Step order"></q-input>
								<div class="hint">This step will be inserted before other steps of the same order number.</div>
							</div>

							<label>Color label</label>
							<q-color
								v-model="stepEdits.color"
								default-view="palette"
								class="my-picker"
							></q-color>

							<div class='Mrow'>
								<q-checkbox v-model="stepEdits.share_requires" label="Check if sharing with partners requires this step to be complete"></q-checkbox>
							</div>
						</div>
					</template>

					<q-separator></q-separator>

					<q-card-actions align="right">
						<q-btn flat label="Cancel" color="primary" v-close-popup></q-btn>
						<q-btn label="Save" color="primary" v-on:click="updateStep" v-close-popup></q-btn>
					</q-card-actions>

					<q-card-actions align="right">
						Remove this step from your workflow 
						<q-btn label="Delete" color="negative" v-on:click="confirmDelete = true"></q-btn>
					</q-card-actions>
				</q-card>
			</q-dialog>


			<q-dialog v-model="confirmDelete" persistent transition-show="scale" transition-hide="scale">
				<q-card class="bg-negative text-white" style="width: 300px">
					<q-card-section>
					<div class="text-h6">Really delete?</div>
					</q-card-section>

					<q-card-section class="q-pt-none">
						This step will be removed from all documents.
					</q-card-section>

					<q-card-actions align="right" class="bg-white">
						<q-btn flat label="cancel" class='text-black' v-close-popup></q-btn>
						<q-btn color="negative" label="Yes" v-on:click="deleteStep" v-close-popup></q-btn>
					</q-card-actions>
				</q-card>
			</q-dialog>


			<q-dialog v-model="showAdd">
				<q-card>
					<q-card-section>
						<div class="text-h6">New Step</div>
					</q-card-section>

					<q-separator></q-separator>

					<div class="Mform">

						<div class="Mrow">
							<q-input outlined v-model="newStepName" label="Name of this step"></q-input>
							<p class="hint"></p>
						</div>
						<div class="Mrow">
							<q-input outlined v-model="newStepShortName" label="Step label"></q-input>
							<div class="hint">This is how the step will be labeled in the list of documents.</div>
						</div>
						<div class="Mrow">
							<q-input outlined v-model="newStepDesc" type="textarea" label="Description"></q-input>
							<div class="hint">Describe this step to aid in understanding what is involved in completing it, and also the document's state when this step is complete.</div>
						</div>
						<div class="Mrow">
							<q-input outlined v-model="newStepOrder" type="number" label="Step order"></q-input>
							<div class="hint">This step will be inserted before other steps of the same order number.</div>
						</div>
						<div class='Mrow'>
							<q-checkbox v-model="newStepShareRequires" label="Check if sharing with partners requires this step to be complete"></q-checkbox>
						</div>

						<div class="row q-gutter-md">
							<div class="col2">
								<q-btn flat color="primary" icon="minus" label="Cancel"  tabindex="0" v-close-popup></q-btn>
							</div>
							<div class="col2">
								<q-btn color="primary" icon="minus" label="Create Step"  tabindex="0" v-on:click="submitNewStep" v-on:keypress="submitNewStep" v-close-popup></q-btn>
							</div>
						</div>
					</div>
				</q-card>
			</q-dialog>

		</div>

