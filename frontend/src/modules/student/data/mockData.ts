import type { StudentProfile, ActiveExam, UpcomingExam, RecentResult, Announcement, CalendarEvent } from '../types';

export const initialStudentProfile: StudentProfile = {
  name: "Kalkidan Mengistu",
  id: "WU/12048/18",
  email: "mengistukalkidan16@gmail.com",
  department: "Software Engineering",
  program: "Bachelor of Science (B.Sc.)",
  semester: "Semester II",
  academicYear: "2025/2026 Academic Year",
  avatar: "https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=256",
  cgpa: 3.84,
  creditsCompleted: 112
};

export const sampleActiveExam: ActiveExam = {
  id: 1,
  courseCode: "SWE-412",
  courseName: "Distributed Systems & Cloud Architecture",
  examTitle: "Final Theory Examination",
  instructor: "Dr. Abraham Getahun",
  date: "Today, June 26, 2026",
  time: "02:00 PM - 04:00 PM",
  durationMinutes: 120,
  totalQuestions: 5,
  totalMarks: 50,
  questions: [
    {
      id: 1,
      type: 'multiple-choice',
      text: "Which of the following describes the key difference between horizontal scaling (scaling out) and vertical scaling (scaling up)?",
      options: [
        "Horizontal scaling increases the capacity of an existing single node, while vertical scaling adds more nodes to the pool.",
        "Horizontal scaling adds more instances or nodes to the system pool, while vertical scaling increases resources (CPU, RAM) on a single physical machine.",
        "Vertical scaling requires a network load balancer, whereas horizontal scaling does not.",
        "Horizontal scaling is only applicable for relational databases, whereas vertical scaling is only for NoSQL."
      ],
      correctAnswer: "Horizontal scaling adds more instances or nodes to the system pool, while vertical scaling increases resources (CPU, RAM) on a single physical machine.",
      flagged: false
    },
    {
      id: 2,
      type: 'multiple-choice',
      text: "In distributed computing, CAP Theorem states that a distributed data store can simultaneously provide at most two out of three guarantees. What does the 'C' stand for, and how is it defined?",
      options: [
        "Complexity: The measure of mathematical difficulty of the database operations.",
        "Concurrency: The maximum number of simultaneous read operations permitted on a single node.",
        "Consistency: Every read receives the most recent write or an error.",
        "Availability: Every request receives a non-error response, but without guarantee that it contains the most recent write."
      ],
      correctAnswer: "Consistency: Every read receives the most recent write or an error.",
      flagged: false
    },
    {
      id: 3,
      type: 'multiple-choice',
      text: "Which protocol is primarily designed to establish distributed consensus among a group of nodes in a fault-tolerant manner?",
      options: [
        "Simple Mail Transfer Protocol (SMTP)",
        "Raft Consensus Protocol",
        "Border Gateway Protocol (BGP)",
        "Transmission Control Protocol (TCP)"
      ],
      correctAnswer: "Raft Consensus Protocol",
      flagged: false
    },
    {
      id: 4,
      type: 'multiple-choice',
      text: "Which of the following is a characteristic of microservices architecture compared to monolithic architecture?",
      options: [
        "Single shared memory heap across all active services.",
        "Tightly coupled deployment where all components must be compiled and deployed as one binary.",
        "Decoupled services interacting over lightweight protocols like HTTP REST or gRPC with independent databases.",
        "Guaranteed zero network latency between components."
      ],
      correctAnswer: "Decoupled services interacting over lightweight protocols like HTTP REST or gRPC with independent databases.",
      flagged: false
    },
    {
      id: 5,
      type: 'text',
      text: "Briefly explain the role of a Load Balancer in modern cloud-native architectures. Include at least two key benefits.",
      correctAnswer: "A load balancer acts as a reverse proxy, distributing incoming network traffic across multiple servers. Two benefits are: 1) High Availability (fault tolerance by routing away from unhealthy nodes) and 2) Scalability (handling increased workloads by distributing requests efficiently).",
      flagged: false
    }
  ]
};

export const sampleUpcomingExams: UpcomingExam[] = [
  {
    id: 2,
    courseCode: "SWE-414",
    courseName: "Software Quality Assurance & Testing",
    instructor: "Abebech Kassa (M.Sc.)",
    examType: "Mid-Term Examination",
    scheduledDate: "Tomorrow, June 27, 2026",
    startTime: "09:00 AM",
    durationMinutes: 90,
    totalQuestions: 25,
    totalMarks: 30,
    status: "Ready"
  },
  {
    id: 3,
    courseCode: "SWE-418",
    courseName: "Mobile Application Development",
    instructor: "Solomon Tekle (Ph.D.)",
    examType: "Practical Laboratory Exam",
    scheduledDate: "June 29, 2026",
    startTime: "02:00 PM",
    durationMinutes: 180,
    totalQuestions: 3,
    totalMarks: 40,
    status: "Pending"
  },
  {
    id: 4,
    courseCode: "SWE-422",
    courseName: "Compiler Design & Construction",
    instructor: "Dr. Birhanu Belay",
    examType: "Final Written Exam",
    scheduledDate: "July 02, 2026",
    startTime: "09:00 AM",
    durationMinutes: 150,
    totalQuestions: 40,
    totalMarks: 100,
    status: "Soon"
  }
];

export const sampleRecentResults: RecentResult[] = [
  {
    id: 5,
    courseCode: "SWE-401",
    courseName: "Advanced Database Systems",
    examTitle: "Final Term Exam",
    score: 92,
    totalMarks: 100,
    percentage: 92,
    grade: "A",
    status: "Passed",
    completedDate: "June 18, 2026",
    questionsReview: [
      {
        questionText: "What does the ACID acronym stand for in DBMS transactions?",
        studentAnswer: "Atomicity, Consistency, Isolation, Durability",
        correctAnswer: "Atomicity, Consistency, Isolation, Durability",
        explanation: "ACID is a set of properties of database transactions intended to guarantee validity even in the event of errors, power failures, etc.",
        isCorrect: true
      },
      {
        questionText: "Explain the main difference between SQL (relational) and NoSQL (non-relational) databases.",
        studentAnswer: "SQL databases are table-based and use structured query language, representing data with a pre-defined schema. NoSQL databases are document, key-value, graph, or wide-column based, and offer flexible schemas for unstructured data.",
        correctAnswer: "SQL databases are table-based and use structured query language, representing data with a pre-defined schema. NoSQL databases are document, key-value, graph, or wide-column based, and offer flexible schemas for unstructured data.",
        explanation: "SQL requires rigid schemas and joins, while NoSQL supports horizontal scaling with non-relational, flexible document/key-value data stores.",
        isCorrect: true
      },
      {
        questionText: "Which normal form requires resolving transitive dependencies?",
        studentAnswer: "Second Normal Form (2NF)",
        correctAnswer: "Third Normal Form (3NF)",
        explanation: "3NF requires that all non-key attributes are fully dependent on the primary key, and there are no transitive dependencies. 2NF focuses on eliminating partial key dependencies.",
        isCorrect: false
      }
    ]
  },
  {
    id: 6,
    courseCode: "SWE-312",
    courseName: "Human-Computer Interaction",
    examTitle: "Practical Project Evaluation",
    score: 45,
    totalMarks: 50,
    percentage: 90,
    grade: "A-",
    status: "Passed",
    completedDate: "June 12, 2026",
    questionsReview: [
      {
        questionText: "What is Don Norman's definition of an 'affordance' in design?",
        studentAnswer: "An affordance is the relationship between the physical properties of an object and the capabilities of an agent that determine how the object can be used.",
        correctAnswer: "An affordance is the relationship between the physical properties of an object and the capabilities of an agent that determine how the object can be used.",
        explanation: "An affordance represents the actionable properties between the user and the system, showing how the interface 'invites' its usage.",
        isCorrect: true
      },
      {
        questionText: "Which heuristic evaluation principle is violated if an app doesn't have an 'Undo/Redo' button for critical actions?",
        studentAnswer: "User control and freedom",
        correctAnswer: "User control and freedom",
        explanation: "Users often choose system functions by mistake and will need a clearly marked 'emergency exit' to leave the unwanted state without having to go through an extended dialogue.",
        isCorrect: true
      }
    ]
  },
  {
    id: 7,
    courseCode: "SWE-320",
    courseName: "Formal Methods in Software Eng.",
    examTitle: "Mid-Term Theory Paper",
    score: 38,
    totalMarks: 50,
    percentage: 76,
    grade: "B",
    status: "Passed",
    completedDate: "May 25, 2026",
    questionsReview: [
      {
        questionText: "What is the primary objective of using Formal Methods in software engineering?",
        studentAnswer: "To mathematically specify, verify, and prove that a software design or code operates strictly within its planned parameters.",
        correctAnswer: "To mathematically specify, verify, and prove that a software design or code operates strictly within its planned parameters.",
        explanation: "Formal methods use mathematical rigor to specify and prove correctness, lowering safety-critical bugs to near zero.",
        isCorrect: true
      }
    ]
  }
];

export const sampleAnnouncements: Announcement[] = [
  {
    id: "ann-1",
    title: "Final Exam Schedule & Hall Allocations Released",
    content: "The official Wollo University Senate has approved the Semester II Examination Timetable. All exams will take place at the KIOT (Kombolcha Institute of Technology) Campus and main Dessie Campus. Software Engineering students will sit for their papers in Computer Lab 3 & 4. Please ensure you carry your student IDs.",
    date: "June 25, 2026",
    category: "Schedule",
    sender: "Registrar Office, Wollo University"
  },
  {
    id: "ann-2",
    title: "System Maintenance: Live Exam Proctoring Server",
    content: "Our online exam portal will undergo a brief scheduled database optimization today at 11:30 PM UTC. There will be no active exams scheduled during this window. Your progress and drafts are securely cached on our server redundantly.",
    date: "June 24, 2026",
    category: "System",
    sender: "ICT Directorate Helpdesk"
  },
  {
    id: "ann-3",
    title: "IMPORTANT: Change in Academic Integrity Policies",
    content: "Wollo University enforces a zero-tolerance policy towards academic dishonesty. For online examinations, our system logs window focus losses, multi-tab access, and webcam feeds. Exceeding 3 tab-switches will flag the student's attempt to the department board automatically.",
    date: "June 22, 2026",
    category: "Urgent",
    sender: "Dean, College of Informatics"
  }
];

export const sampleCalendarEvents: CalendarEvent[] = [
  {
    id: "cal-1",
    title: "Distributed Systems Exam (SWE-412)",
    date: "2026-06-26",
    type: "Exam",
    description: "Final written evaluation of Distributed Computing, CAP, Microservices, and Cloud Storage design."
  },
  {
    id: "cal-2",
    title: "Software QA & Testing Mid-Term Exam",
    date: "2026-06-27",
    type: "Exam",
    description: "Assessment on unit testing, automation suites, integration testing strategies, and QA metrics."
  },
  {
    id: "cal-3",
    title: "Clearance & ID Card Renewal Deadline",
    date: "2026-06-30",
    type: "Deadline",
    description: "Ensure all library clearances are completed before terminal evaluations."
  },
  {
    id: "cal-4",
    title: "Ethiopian Martyrs Commemoration (Holiday)",
    date: "2026-07-01",
    type: "Holiday",
    description: "National public holiday. All university offices and campuses are closed."
  },
  {
    id: "cal-5",
    title: "Compiler Design Final Exam",
    date: "2026-07-02",
    type: "Exam",
    description: "Final comprehensive compiler construction exam (Lexical, Syntax, Semantic, and Code generation phases)."
  }
];
