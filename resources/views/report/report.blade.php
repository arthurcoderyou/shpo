<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php
        $date_time_text = "";
        $date_time_text = date('l m/d/Y', strtotime(now())); // default is now

        if (!empty($start_date) && !empty($end_date)) {
            if ($start_date == $end_date) {
                $date_time_text = date('l m/d/Y', strtotime($start_date));
            } else {
                // Concatenate two date values correctly
                $date_time_text = date('l m/d/Y', strtotime($start_date)) . ' - ' . date('l m/d/Y', strtotime($end_date));
            }
        } else if (!empty($start_date)) {
            $date_time_text = date('l m/d/Y', strtotime($start_date));
        } else if (!empty($end_date)) {
            $date_time_text = date('l m/d/Y', strtotime($end_date));
        }


         
 


    ?>


    <title>Registration report - {{ $date_time_text }}</title>
    <style>



        /* Header and footer styles */
        @page {
            margin: 50px 40px;
            font-family: Arial, Helvetica, sans-serif;
        }

        header {
            position: fixed;
            top: -20px;
            left: 0px;
            right: 0px;
            height: 10px;
            text-align: left;
            color: gray;
            line-height: 3px;
            font-weight: bold;
        }

        footer {
            position: fixed;
            bottom: -20px;
            left: 0px;
            right: 0px;
            height: 10px;
            text-align: left;
            color: gray;
            line-height: 3px;
        }

        .content {
            margin-top: 0;
        }

        /* Page numbering */
        .page-number:before {
            content: "Page " counter(page) ;
        }


        body {
            font-family: Arial, sans-serif;
        }

        /* Timeline container */
        .timeline {
            position: relative;
            max-width: 100%;
            margin: 3px 0;
        }



        /* Individual timeline container */
        .timeline-item {
            padding: 3px 3px;
            position: relative;
            background-color: inherit;
            width: 100%;
        }


        /* Timeline content */
        .timeline-item-content {
            padding: 3px;
            background-color: white;
            position: relative;
            border-radius: 6px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Date text */
        .timeline-item .date {
            color: #007bff;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
        }

        /* Date text */
        .timeline-item .day {
            color: #000122;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
        }

        /* Timeline title */
        .timeline-item h3 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }

        /* Timeline title */
        .timeline-item ul li  {
            font-size: 14px;
            color: #555;
            text-align: justify;
        }

        /* Timeline description */
        .timeline-item p {
            margin-top: 5px;
            font-size: 14px;
            color: #555;
            text-align: justify;
        }


        .text-center{
            text-align: center;
        }

 
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            font-size: 1rem;
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #4CAF50; /* Green bottom border */
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1; /* Light hover effect */
        } 

    </style>
</head>
<body>

    <!-- Header -->
    <header style="font-weight: bolder">
        <small style="font-size: .9rem; font-weight: bolder; color: black;">SHPO Project Review Report {{  $date_time_text }} </small>
        <hr style="background: black; ">
    </header>

    <!-- Footer -->
    <footer  style="font-weight: bolder">
        <hr style="background: black; ">
        <small style="font-size: .9rem; font-weight: bolder; color: black;" class="page-number"></small>
    </footer>

    <div class="conten">

        <!-- layout -->
        <div class="timeline">

            <table>
                {{-- <thead>
                    <tr>
                        <th>Player Registration ID</th>
                        <th>Tournament ID</th>
                        <th>Registered Options</th>
                    </tr>
                </thead> --}}
                <tbody>
                    @foreach($data as $review)
                        <tr>
                             
                            
                            <td style="text-wrap: wrap; max-width: 30%;">
                                <strong>{{ $review['project'] }}</strong> <br>
                                <i>{{ $review['project_status']  }}</i> <br>

                                {{ $review['reviewer']['name'] }} <br> 
                                {{ $review['reviewer']['email'] }} <br> 
                            </td> 

                            <td style="text-wrap: wrap;">

                                <!-- Check if admin review  -->
                                @if($review['admin_review'] == true)

                                    <strong>{{ $review['project_review'] }}</strong> <br>
                                    <span style="margin-top: 2px; display: block;">
                                        {{ ucfirst($review['review_status']) }} 
                                    </span>
                                    <br>
 

                                    <span style="margin-top: 2px; display: block;">
                                        Project updated at
                                        {{ date('l m/d/Y', strtotime($review['review_created_at'])) }}
                                    </span>
                                    
                                    <br>

                                    @if(!empty($review['next_reviewer']))
                                
                                    

                                        <span style="margin-top: 2px; display: block;">
                                            Next Reviewer: 
                                            {{ $review['next_reviewer'] }}
                                        </span>
                                        
                                        <br>


                                        <span style="margin-top: 2px; display: block;">
                                            Project updated at
                                            {{ date('l m/d/Y', strtotime($review['reviewer_due_date'])) }}
                                        </span>
                                        
                                        <br>    


                                    @endif 


                                <!-- if reviewer review -->
                                @elseif($review['reviewer_review'])


                                    <strong>Reviewed</strong> <br>
                                    <span style="margin-top: 2px; display: block;">
                                        {{ ucfirst($review['review_status']) }} 
                                    </span>
                                    <br>

                                    <span style="margin-top: 2px; display: block;">
                                        {{ ucfirst($review['project_review']) }} 
                                    </span>
                                    <br>

                                    <span style="margin-top: 2px; display: block;">
                                        {{ date('l m/d/Y', strtotime($review['review_created_at'])) }}
                                    </span>
                                    
                                    <br>

                                @else 


                                    <span style="margin-top: 2px; display: block;">
                                        @if($review['review_status'] == "submitted")
                                            Submitted
                                        @elseif($review['review_status'] == "re_submitted")
                                            Re-submitted
                                        @else
                                            Draft
                                        @endif 
                                        by {{ $review['reviewer']['name'] }}  
                                    </span> <br>
                                                
                                    <span style="margin-top: 2px; display: block;">
                                        Project <span class="font-bold text-yellow-500">Submitted</span> 
                                        at {{ \Carbon\Carbon::parse($review['review_created_at'])->format('d M, h:i A') }}  
                                    </span> <br>

                                    <span style="margin-top: 2px; display: block;">
                                                {{ $review['project_review'] }}
                                    </span>
                                @endif

                            </td>


                            <td style="word-wrap: break-word; max-width: 30%;">
                                <strong>Review Attachments</strong> <br> 
                                <ul style="margin-top: 3px">     
                                    @if(!empty($review['review_attachments']))

                                        @foreach($review['review_attachments'] as $attachment)
                                            <li>{{ $attachment }}</li> 
                                        @endforeach
                                    @else 
                                        <li>No attachments found</li>
                                    @endif

                                </ul>
                            </td>


                        </tr>
                    @endforeach
                </tbody>
            </table>


          




        </div>
        <!-- end of layout -->

    </div>

</body>
</html>





