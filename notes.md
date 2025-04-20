# EMAIL CODING 

gsab bgjn lola xcgp

code for email

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=arthurcervania13@gmail.com
MAIL_PASSWORD=gsabbgjnlolaxcgp
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="arthurcervania13@gmail.com"
MAIL_TO_ADDRESS="arthurcervania13@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"



# Message by sir Vicente 

 Hi Arthur, 

Here is the typical Section 106 process for Guam SHPO.

The Guam State Historic Preservation Office (SHPO) follows the standard Section 106 review process as outlined in the National Historic Preservation Act (NHPA).his process ensures that federally funded or permitted projects consider their impact on historic properties. While the general steps are consistent nationwide, Guam SHPO may have specific procedures or requirements tailored to the island's unique cultural and historical context. Key Steps in the Section 106 Review Process:
1.	Initiation of the Process: 
o	The federal agency determines if the proposed project qualifies as an undertaking under Section 106. - f so, the agency notifies the Guam SHPO and other consulting parties to initiate the review.
2.	Identification of Historic Properties: 
o	Define the Area of Potential Effects (APE) for the project. - Conduct surveys to identify properties within the APE that are listed or eligible for listing in the National Register of Historic Places. - Consult with the Guam SHPO, local governments, and other stakeholders to gather information on potential historic properties.
3.	Assessment of Effects: 
o	Evaluate how the undertaking may affect identified historic properties. -Determine if the effect is adverse, meaning it may diminish the property's integrity regarding its location, design, setting, materials, workmanship, feeling, or association.
4.	Resolution of Adverse Effects: 
o	If adverse effects are found, consult with the Guam SHPO and other parties to develop measures to avoid, minimize, or mitigate these effects. - his often results in a Memorandum of Agreement (MOA) outlining the agreed-upon measures.
5.	Implementation and Monitoring: 
o	Carry out the agreed measures. - Monitor the project's impact on historic properties and ensure compliance with the MOA. Guam-Specific Considerations:

Given Guam's rich cultural heritage, including significant Chamorro sites, the Guam SHPO places emphasis on:
•	Early Consultation: Engaging with the SHPO early in project planning to identify potential concerns and streamline the review process.
•	Public Involvement: Encouraging public participation to gather diverse perspectives on historic preservation issues.
•	Cultural Sensitivity: Ensuring that projects respect and preserve sites of cultural importance to the Chamorro people and other communities on the island. or detailed guidance and specific procedures, it's advisable to contact the Guam SHPO directly or visit their official website. They can provide resources, application forms, and additional information pertinent to the Section 106 review process in Guam. y adhering to these procedures and collaborating with the Guam SHPO, federal agencies and project proponents can ensure that their undertakings are compliant with the NHPA and respectful of Guam's unique historical and cultural resources.


Vicente Ada II, PE, CES
Environmental Engineer
KHLG & Associates, Inc.
671-478-5454 ext. 277






# Proposed new project process

* pending is the standby
* submitted: project submitted by a person
* in_review: project is now being reviewed by a reviewer
* approved: project is approved             
* rejected: project is rejected
* completed: project is completed


> User Workflow: 
1. Create a project     [pending]
    [Project] 
    - columns: 
        - name 
        - description
        - federal_agency
        - status    [default:draft] [submitted] [in_review] [approved] [rejected] [completed] [cancelled]
        - created_by
        - updated_by
        - created_at
        - updated_at
        > project attachments 
        [ProjectAttachment]
        - columns: 
            - attachment
            - project_id (FK to projects)
            - created_by
            - updated_by
            - created_at
            - updated_at

<!-- Optional -->
<!-- 2. Enter additional project information    [pending]
    [HistoricProperty] can be multiple
    - columns: 
        - project_id (FK to projects)
        - name
        - location
        - description
        - eligibility_status (enum: eligible, not_eligible, listed)
        - created_by
        - updated_by
        - created_at
        - updated_at
        > effect assessment of that historic property
        [EffectAssessment] can be multiple
        - columns: 
            - project_id (FK to projects)
            - historic_property_id (FK to historic_properties)
            - effect_type (enum: no_effect, adverse_effect, beneficial_effect)
            - description
            - created_by
            - updated_by
            - created_at
            - updated_at
            > mitigation messures that will be done after assessing the effects 
            [ResponseMessure] can be multiple
            - columns:
                - effect_assessment_id (FK to effect_assessments)
                - description
                - created_by
                - updated_by
                - created_at
                - updated_at -->
<!-- ./ Optional -->


3. Submit the project       [submitted]
4. The reviewer opened the project  [in_review]
5. The reviewer entered the project and analyze it upon giving a decision   
    [Review] : data created
    - columns: 
        - reviewer_id
        - project_id (FK to projects)
        - project_review
        - project_review_status     [pending] [approved] [rejected]
        - viewed    [true] [false]
        - created_by
        - updated_by
        - created_at
        - updated_at
        > review attachments 
        [ReviewAttachment] can be multiple
        - columns:
            - file name
            - description
    After the review, it will be sent back to the email of the project submitter/owner

6. The reviewer 
    <!-- optional -->
    <!-- [Stages] : This is the admin customization of project review stages  
    - columns:
        - order     : denotes the order of the reviewer if she will be the first or last 
        - name
        - description
        - created_by
        - updated_by
        - created_at
        - updated_at
        > requirement_details   : denotes the requirements needed for this project stage 
        [StageRequirement] can be many
        - columns: 
            - requirement
            - created_by
            - updated_by
            - created_at
            - updated_at
        > supporting_attachments    : denotes the supporting documents and it can be multiple
        [SupportingDocument] can be many
         - columns: 
            - document
            - created_by
            - updated_by
            - created_at
            - updated_at -->
    

    [ProjectReviewer] : connector of stage to the reviewers
    - when the user is added as a project reviewer, he will then be one of the default project reviewer on the project
    - columns:  
        - project_review_stage_id
        - order
        - user_id

    [ProjectStageReviewStatus] : connector of stage to the reviewers review and project status\
    - NOTE: this is a single record data that acts as an identifier if that project had alraedy passed that stage of project review
    - columns: 
        - project_review_stage_id (FK to project_review_stages)
        - project_id (FK to projects)
        - project_review_id
        - status     [pending] [approved] [rejected] 
        - created_by
        - updated_by
        - created_at
        - updated_at


Once completed all stage of review and verified, the project will be marked as approved. Then the next process is MOA 



# Notes to consider
There is an active job at the backend that will not let two people review the same project at the same time. There is a status on the review stage status 


# users
Admin
User
Reviewer




# the admin can make a project to be approved and all of its project_reviewer stages

# the admin can also edit the project reviewers of a certain project

# add counters



# February 6 meeting additions and recommendations
    The process is approved

> Add a dashboard
- showing pending actions
- showing pending projects
- showing pending actions that needs to be done
- showing assigned or pending task
- how many days that the task is assigned to me
- how many days on the approval process
- status report on projects
- create a separate page or widget, a dashboard to notify the users what is pending or not


    # What to add in dashboard
    - Create a livewire dashboard for 
        - No role user dashboard
            - page notifying them to wait for the admin to verify their identity (give them a role) and 
        - Admin dashboard
            - Users without role count (verified users with emails is counted)
            - Project counters
            - Pending project reviews
            - Project status 
            - Number of Reviewers with pending reviews to projects 
                - Ability to warn them that they have pending project reviews
                - Deadline on projects with past due or review date 
        - Reviewer dashboard
        - User dashboard 
        




> Project recommendations
- On the projects table ,add a column for the past due or review due date when its passed due or like 21 day from the day it is submitted, looks like that . To note if the project is flagged to have a due date or not. We can actually set a global timer (which can also be local). The gloabl timer can be customized to be longer or shorter. 
There are like a certain ammount of days to get a response back from the reviewer. Then create a trigger if that review counter will reset after all reviewers reviewed it or when one reviewer reviewed it. Like a timer to their response rate in time.
- global timer
- review response rate timer
- time response rate indicators 
- timer can be customized
- Project name must be not modified, (but possible with admin access)
- project number
    - based on admin SHPO (based on shpo tracking services)
    - based on custom given project number (system number)
- When everything is approved, they must have the ability to uplaod files or attachmetns. For signed aprroval documents.
- User uplaod section 
- Agency upload section
- identification of user and agency upload
- Upload of documents is from one of hte reviewers but hte document is signed by the administrator
- Upload of documents must be 20mb atleast


    # What to add in projects
    - global time settings
    - Notes to tell that the project is in due.
        - project submitter_response_timer_days converts 




> Project submission and documents
- documents that are deleted cannot be deleted when it is submitted already because that data must be saved for documentations.
(restrictions after submission of projects)
- changing project name after submission can only be done by the admin
- once a project is reviewed, all documents cannot be deleted. The submitter can still send additional documents. 

> Signup & Login
- After logging in, the user and reviewer signup role will be decided by the administrator. 
- Landing page must be the same on all kinds of users
- change the program picture into guam
- the signup and login of user is used and then it must notify the admin for verification. so that they can be accepted as user or reviewer, Until then, that user will be a logged in user only


> Activity Logs
- add last logged in time of user

> Admin
- The more the admin can see details about the system, the better
    - when it is added, how long it is there, and every update about it.

> User 
- Each user can only see projects they created

> Search Query
- Search by name, search by project, search by village
- Map Search
    - This is an advanced search where it will locate locations for mapping. Converting the addresses into coordinate points for projects where it is located.
    - Allow the user to define a location and display on a google map for the project. 
    - Click a location and show up in a google map.
    - Like go to google map and pin it into the database.
- Search by location, village, or city or by contractor

> Storage 
- website storage after a year must have a way in a website to archive data in bulk. archive and download the data. purge and download the data for the shpo. 
- bulk deletion of data too .
- if they purge the data, the data of the record on teh database of the project name and teh year with teh document names is there but the data cannot be downloaded. The actual project attachment will remain but the actual data attachment is purged. 



# Create 
project 
in_response

project section for User to see the projects that are pending of responses
- Projects for users that needs to send a resubmission



# Simplify the submission and addition of records 




### The roles and permissions is incomplete ###






### ActivityLog



Here are the **ActivityLog** entries for each of the requested actions:  

---

### **1️⃣ User Registration (New User Registered)**
```php
ActivityLog::create([
    'log_action' => "New user \"".$this->name."\" has registered.",
    'log_username' => Auth::user()->name,
    'created_by' => Auth::user()->id,
]);
```

---

### **2️⃣ User Login**
```php
ActivityLog::create([
    'log_action' => "User \"".Auth::user()->name."\" has logged in.",
    'log_username' => Auth::user()->name,
    'created_by' => Auth::user()->id,
]);
```

---

### **3️⃣ Forgot Password Request**
```php
ActivityLog::create([
    'log_action' => "User \"".$this->email."\" has requested a password reset.",
    'log_username' => $this->email, // Email since user may not be logged in
    'created_by' => null, // No user is logged in
]);
```

---

### **4️⃣ Reset Password**
```php
ActivityLog::create([
    'log_action' => "User \"".Auth::user()->name."\" has reset their password.",
    'log_username' => Auth::user()->name,
    'created_by' => Auth::user()->id,
]);
```

---

### **5️⃣ Verify Email**
```php
ActivityLog::create([
    'log_action' => "User \"".Auth::user()->name."\" has verified their email.",
    'log_username' => Auth::user()->name,
    'created_by' => Auth::user()->id,
]);
```

---

### **6️⃣ Update Profile Information**
```php
ActivityLog::create([
    'log_action' => "User \"".Auth::user()->name."\" has updated their profile information.",
    'log_username' => Auth::user()->name,
    'created_by' => Auth::user()->id,
]);
```

---

### **7️⃣ Update Password (From Profile Page)**
```php
ActivityLog::create([
    'log_action' => "User \"".Auth::user()->name."\" has updated their password from the profile page.",
    'log_username' => Auth::user()->name,
    'created_by' => Auth::user()->id,
]);
```

---

### ✅ **Notes:**
- `Auth::user()->name` ensures the currently logged-in user's name is logged.  
- `created_by` stores the **user ID** for tracking.  
- For **Forgot Password**, we log `email` since the user is not authenticated.  

Would you like me to add anything else, such as **IP address logging** or **timestamps**? 🚀





### notes on Feb 18 2025 meeting about shpo

- the dashboard on user auth but no role has a bug and must be fixed
- the farthest and nearest word must be fixed 

- add gis files into google map in order to add gps shapes on the google map of hte project
- tiff files 
- gps data files


- adjust federal agancy in the project part
    - adjust it to be company 
    - and then a choice to identify it as either a company or a federal agency


Add to the log when the person had submitted it again

Separate signin for reviewers

Update the review message because project approved is misleading ebcause the project is just moved on to the next reviewer and does not automatically being approved
Update the SHPO number because the project SHPO number is to be added by the first reviewer of the project

Add a different signup page for reviewers



add another status on the project to add a selection
    - federal project 
    - company


# updates

Porject number nad SHPO number are now only visible to reviewers
Shpo number is now to be added by the first reviewer of hte project 

THe project had also bee nadjusted to have company type, such as federal rpoject and company

The review list had also been updated to reduce misleading content 
making it more guiding and now adding data for user project submission / re-submission and project review status updated from project status to review status and also showing the next reviewer if hte there is a next reviewer on the list

THere is also now a separate register page for reviewers
Users and reviewers can now register and saved with different requested role 

On teh admin side, there is also a section showing the users that is intended to be users and users that is intended to be reviewers 
THe admin can click it and it will show on to the list of users newly registered requesting approval for the roles




# Roles and Permissions

> User
user create 
user edit 
user delete
user list view


> Permission
permission create
permission edit
permission delete
permission list view

> Role
role create
role edit
role delete
role list view
role view permission
role update permission

- on admin , permission is removed (only visible on DSI God Admin)


> Dashboard
dashboard view
dashboard counters
dashboard notifications


> Notifications
notifications mark as read
notifications delete

- on review, there is also an additional that the user must be the creator of hte rpoject to mark the message as viewed

> Project
project create
project edit 
project delete
project list view

project review list view
- allows to see the review list at the bottom of the project

project update list view
- allows USER to see the projects for the user to update

project review 
- allows REVIEWER to review the project

project review add attachment
- allows USER to add attachment

project submit
- allows USER to submit project

project review delete attachment 
- allows USER to delete attachment

project review mark as viewed
- allows USERS to mark the review as viewed

project reviewer list view
- allows REVIEWER to view list of project to review 

project approval override 
- allows ADMIN override of project approval

project restart override
- allows ADMIN the restart of project 

project view
- allows all to view project

project reviewer edit
- allows reviewers to edit project reviewers in the website

> Review 
review list view
review list project details view

> Reviewer
reviewer list view
reviewer create
reviewer edit
reviewer delete
reviewer apply to all


> Timer
timer list view
timer create
timer edit
timer delete 
timer apply to all

> Activity Logs
activity log list view
activity log delete

> Profile
profile update information
profile update password
profile delete account







# tracking metrics 

# Review timeline 

The review timeline can be customized by the user because sometimes there are different review timelines. It should be the submitter setting the clock for the reviewer to send a feedback to the user request. 

> My adjustment will be the sender can adjust the time of the reponse time for the reviewer to send a review, and the reviewer can also adjust the time for the sender to send a new submission

There should be no classified information going to SHPO. CUI can be passed on to the system but it must be marked as CUI. CUI trainee must signed it as non-disclosure information, 

DOD side and SHPO side 

CUI documentation 


They are not advised to submit location information and it is up to the releaser of the information if its safe to share to the shpo organization. 

Can make there work more effiecient 



# Update the email 

Update the reviewer for distribution list to notify other users that they must update or review the file and after reviewing, they can notify back. 

> Solution: add an option for the reviewer to pass the project to be reviewed by other reviewers first or add a new reviewer to the reviewer list 



Normally, they accompany physical documents. Years to come, they can see the files or messages or documents recieved.

Export timestamp on the reviews to see when it is submitted or been sent for reviews for reporting porpuses 


Type of documents can also be added on the one being sent on what type of document can be used



# Additional Fixes
Notification on applying project reviewers on admin side to notify all project submitters on their project and project reviewers about the change

UPDATES APPLIED 
On project apply to all, updating the project reviewers list will now notify users and reviewers, and if a reviewer is the new current reviewer based on the order , a notification for review request will be sent to that reviewer for review.


The update on project reviewers are also added on the notifications table

Global Review Reviewer List Update

Single Project Reviewer List Update
On the admin side, the user can now notify all about the update on the reviewer list 


> Submitter has an option to update reviewer response time
- section at  project create/edit

> Reviewer has an option to update submitted response time
- section at project review create or with project create/edit

> Added deletion logs and order update list logs


> To fix the distribution, we had added a new link for the reviewer to update the reviewers list and order so that they can see the current reviewers for the project and can already update the reviewers and their order. When a reviewer is deleted, a reviewer will automatically be selected as the current reviewer. 


> export option for the reviews for documentation of reviewers
 

> added view timestamp on reviews 
> added review notes by admin when project reviewers had been added to the system



Here's a structured report based on the updates and features you have implemented on the website:  


--- SUMMARY

# **Project Review System Update Report**  
**Date:** {{ date('Y-m-d') }}  
**Prepared by:** [Your Name]  

## **Overview**  
This report summarizes the updates and enhancements implemented in the project review system, focusing on notifications, logging, reviewer management, and export functionalities.

---

## **1. Notification System Enhancements**  

### **🔹 Notifications on Project Reviewer Updates**  
- When the project reviewers list is updated, notifications are sent to:
  - **Project Submitters** (to inform them about the assigned reviewers).  
  - **Project Reviewers** (to notify them about their role in the review process).  
- If a reviewer becomes the **new current reviewer** based on order, a **review request notification** is sent to them.

### **🔹 Notifications Table Update**  
- The notifications table now logs:
  - **Global Reviewer List Updates**  
  - **Single Project Reviewer List Updates**  
- Admins can now **notify all stakeholders** about changes in the reviewer list directly from the system.

---

## **2. Reviewers’ Response Time Management**  

### **🔹 Submitter's Role:**  
- Submitters can now update the **reviewer’s response time** for better tracking.

### **🔹 Reviewer’s Role:**  
- Reviewers can now update the **submitted response time** to reflect their review progress.

---

## **3. Logging System Improvements**  

### **🔹 Deletion Logs**  
- When a **reviewer or project is deleted**, the action is logged for reference and accountability.

### **🔹 Order Update Logs**  
- Any changes in the **reviewers' order** are now recorded to track modifications over time.

---

## **4. Reviewer List and Order Management**  

### **🔹 New Link for Reviewer List Management**  
- A dedicated page allows reviewers to:
  - View **current reviewers** assigned to a project.  
  - **Update the reviewer list and order** as necessary.  

### **🔹 Automatic Reviewer Selection**  
- If a reviewer is **removed from the list**, the next available reviewer in order **automatically becomes the current reviewer**.

---

## **5. Export Functionality for Documentation**  

### **🔹 Export Review Reports**  
- The system now supports an **export feature** that allows administrators to download:  
  - **Review details** for documentation.  
  - **Reviewer lists and response times** for record-keeping.  

---

## **Conclusion**  
The recent updates significantly enhance the **review process workflow**, ensuring that all project stakeholders remain informed and that project reviews are managed efficiently. These changes improve **accountability, tracking, and automation**, making the review process **more structured and transparent**.  

🚀 **Next Steps:** Continue monitoring system performance and gather feedback for further improvements.  
 
---

Let me know if you need modifications or additional details! 😊




### NOTES ON MEETING AT 12 March 2025


# Document Types 

Work Plan

Draft Report

Final Report

HAAB/HAER

Inadvertant

Section 106


### To be updated for SHPO program 


On the user side, the output did not show so the user 


There is a bug when dealing with APPLY TO ALL FUNCTIONALITY
> BUG FIXED 
APPLY TO ALL FUNCTIONALITY





# NEW CONCEPTS 

- the project can have different submittals 

The project can have a submission of Document Type: [Work.Plan] or a submission of Document Type: [Draft.report] or other document types, and each Document Type has a different reviewer

Project Document Type reviewer 


The idea is that every project submission have a project document type added where the project reviewer is determined


ProjectDocuments 
- Project has many relationship to ProjectDocuments 



Add a text 
Project Document Type 

Adjust the data because 

The project can have many documents 
Document Types can have document files 


> Bring back the review and submitter time to the reviewer side
The submitter must not be the one that sets how much time the government will take time of the reviewer to review, It must be on the reviewer.

> Bring back the In-Review to also add project in review

> Fix the exported pdf file of the reviews
Print the page instead of showing the reviews 


> Add on the project page a subscription type of email to notify users and make them being updated to the project / or a project. 
The reviewers and createor is being notified, and we would like to add external emails who are not users to be notified to the updates of the project. 


> There should be a user type that is between reviewer and admin that can see the project and add or modify if something is missed 
Name it as Liason user 

> On the tracker, add Metrics 
Being able to track metrics based on user activity  
Average Response time

Response time is calculated based on the time on the review model 
Review from user is typically the resubmission of data


Average Review time 
Average time for project to be completed 

>>> Another Solution added, 
Add metrics for the project time for review and response || This is in hours 





> Possible target of the website is khlg website 





### Code base Development March 18 2025 ###
> Add DocumentType 
DocumentType


> Adjustment on Project
- add project submission.
ProjectSubmission 
model to allocate the project attachment submitted documents and categorizes them based on the created_at timestamp


ProjectDocument 
- connects the project attachment and the connected document type 

add project_document_id on project_attachments 


> Update the review and make it as Audit Trail 
The review will have additional details because it now works as the documentation on what is being done on the project and the updates of it 


> On the Login , add 2FA on logging in for more security


> Notification email on project subscribers
Project Submission
Project Re-submission
Project Review
Project update


> Project show restrictions
Users that are non-admin will cannot download files from project that they had not created


> Admin side update 
On Porject show, the reviewer and response time is now showing for admin


> Fixed the bug  on deleted reviewers 
The reviewers deleted from the project reviewers list will now be notified on accessing the project review that they are no longer project reviewer for that project


> Permission for document type create, delete, view and edit
document type create
document type edit
document type delete
document type list view


> DSI God Admin is now not shown 


### TO BE ADDED

> on the Authenticator, add a data about an option to save the device credentials 

> Also tehre is a bug that you can still go to the OTP Verification route even though you are already verified 

> On the project create, make the creation of the project to be just one document type upload and on the edit too 

The name will be Submission Type

> Update the name DSI God Admin to DSI Global Administrator



## FIXED 
going to the 2fa verification page will lead back to dashboard if already authenticated 

THe project create and edit has updated values and now has only one document attachment  



### 

1.	Account Sign-up (Reviewer/User)
2.	Login (Reviewer/User)
3.	User Submission 
4.	SHPO Review


# Fixed some logical bugs ||||| GET FROM THE HOSITNG COPY
Dark Mode removed 
Not allowed to submit when there are no project reviewers 
Fixed email verification first before 2fa verification 
Fixing the dark mode 
Added errors



# Notes on teh updates Needed from the meeting at March 28 2025
1. Adding GIS to the project mapping API. 
2. Forum for Project Discussions            || Create, Reply, Edit, Delete 
    The Forum also has private and public,  public can be accessed by All, private is only for the Admin and Reviewers. 
    THe update is realtime , the discussion list is being shown without reloading the page making users view the real time discussions added by Website ADmin, Reviewers or Users connected to the project
    Admin,DSI God Admin, Reviewers can sort the values to only show private 
    Realtime notifications are added as well: There is a notifications that is displaying at teh top side of teh websitee for Users that are either Admin, Reviewer (that is a reviewer to the project) and project owner.
    Reply Control, only users part of the project can send a reply. Admin is excemplted on this condition 

3. Project Timer Process Adjustment: The rules is updated on the time reset
4. Project Review Process Adjustment: New request on review options such as temporary reject that rejects the project but the timer is not stopped
5. Project Report to PDF 
6. Project Report Additional Metrics: Project Status with time date metrics.
7. Project Approval Form and E-Signature Implementation.
8. Project Reviewer Auto Adjust Based on the selected submission document type: Ex. Submission of Work Plan will automatically change the order or reviewers or add specific reviewers to that project that are not existing on other type of document type submission. 
9. RFI Added on the review: Request for Information on the project when sending a project rejection review: This can be discussed further for the adjustment of the project review process. 
10. Project review export
11. Project Cut Off Time




# From the meeting 
A cut off time on 4pm Monday to Friday 
- incorporating a process to not allow users to submit a project. Also adding a disclaimer that submitted projects will be seen for the next day.  

GIS 
- Project Map 
- Map Layers
- Map Multiple Sites 
- The project map details must be 



# Needed in GIS Integration 
Interactive Maps – Display maps with zoom, pan, and layer controls.

Geolocation – Track user locations and provide relevant geographic data.

Spatial Analysis – Analyze geographic patterns, distances, and relationships.

Data Visualization – Overlay data on maps using markers, heatmaps, and polygons.

Routing and Navigation – Provide directions and route optimization.

Geocoding & Reverse Geocoding – Convert addresses into coordinates and vice versa.





Apply the features needed first then we will fix the bugs later on 




# Bugs found 

There is no notification for the admin if the project reviewer, project timer and document types are setup already .
    - 0 document types 
    - 0 project reviewers 
    - project timers incomplete setup (submission timer, closing and starting timer)

    => Add it on the dashboard for it to be fully added to the website before any submissions is applied 


Create full messages on teh deletion and if possible, only provide the idea to disable the values instead of deleting it completely 

Everytime a user is deleted, the data or project reviewing that connected into it must be updated to have the updated details about the reviewer and notifications for the user as well 

User 
    ProjectReviewer 








