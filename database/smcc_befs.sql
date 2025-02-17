-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 08, 2025 at 03:16 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smcc_befs`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `asd` ()   BEGIN
	#Routine body goes here...
SELECT students.lrn_num as lrn_num, 
students.fname as fname, 
students.lname as lname,
year_level.description as ydesc,
course.description as cdesc,
section.description as sdesc,
student_score.average as average,
student_score.level as level
from student_score,students,year_level,course,section,subjects WHERE
student_score.stud_id = students.id AND
student_score.sub_id = subjects.id AND
students.year_level_id = year_level.id AND
students.section_id = section.id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `id` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `year_level_id` int(11) DEFAULT NULL,
  `school_year` varchar(255) DEFAULT NULL,
  `date_entry` datetime DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `code_no` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`id`, `description`, `year_level_id`, `school_year`, `date_entry`, `status`, `code_no`) VALUES
(1, 'Bachelor of Science in Criminology', 1, '1', '2024-12-03 02:30:23', 'Active', 'BSCRIM'),
(4, 'Bachelor of Science in Information Technology', NULL, NULL, '2024-12-17 11:23:58', 'Active', 'BSIT');

-- --------------------------------------------------------

--
-- Table structure for table `dean_course`
--

CREATE TABLE `dean_course` (
  `user_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dean_course`
--

INSERT INTO `dean_course` (`user_id`, `course_id`) VALUES
(0, 1),
(6, 1),
(3, 1),
(4, 1),
(5, 4),
(6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `faculty_course_school_year`
--

CREATE TABLE `faculty_course_school_year` (
  `user_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `school_year_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `faculty_course_school_year`
--

INSERT INTO `faculty_course_school_year` (`user_id`, `course_id`, `school_year_id`) VALUES
(2, 1, 1),
(4, 1, 1),
(5, 1, 1),
(7, 4, 1),
(8, 4, 1),
(9, 1, 1),
(10, 4, 1),
(10, 4, 1),
(12, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `faculty_subjects`
--

CREATE TABLE `faculty_subjects` (
  `id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `subjects_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `assigned_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_subjects`
--

INSERT INTO `faculty_subjects` (`id`, `faculty_id`, `subjects_id`, `course_id`, `school_year_id`, `assigned_date`) VALUES
(19, 2, 1, 1, 1, '2024-12-25 16:29:04'),
(20, 9, 2, 1, 1, '2024-12-25 17:13:30'),
(23, 8, 12, 4, 1, '2024-12-25 19:30:25'),
(24, 7, 10, 4, 1, '2024-12-28 15:52:50'),
(28, 2, 2, 1, 1, '2025-01-08 18:23:00');

-- --------------------------------------------------------

--
-- Table structure for table `question_answer`
--

CREATE TABLE `question_answer` (
  `id` int(11) NOT NULL,
  `question` varchar(255) DEFAULT NULL,
  `answer` varchar(255) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `faculty_id` int(11) DEFAULT NULL,
  `option1` varchar(255) DEFAULT NULL,
  `option2` varchar(255) DEFAULT NULL,
  `option3` varchar(255) DEFAULT NULL,
  `option4` varchar(255) DEFAULT NULL,
  `level` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `question_answer`
--

INSERT INTO `question_answer` (`id`, `question`, `answer`, `subject_id`, `faculty_id`, `option1`, `option2`, `option3`, `option4`, `level`) VALUES
(1, 'Which of the following is true about the principle of \"double jeopardy\" in Philippine criminal law?', 'It prohibits multiple trials for the same offense', 1, 2, 'It allows for multiple trials for the same offense', 'It prohibits multiple trials for the same offense', 'It applies only to serious offenses', 'It applies only to civil cases', 'PREBOARD1'),
(2, 'Which of the following is NOT a category of penalties in the Revised Penal Code of the Philippines?', 'Perpetual penalties', 1, 2, 'Afflictive penalties', 'Correctional penalties', 'Perpetual penalties', 'Light penalties', 'PREBOARD1'),
(3, 'Which term refers to the process of settling a criminal case without a trial?', 'Plea bargaining', 1, 2, ' Conviction', 'Acquittal', 'Plea bargaining', 'Sentencing', 'PREBOARD1'),
(4, 'What does the principle of  \"presumption of innocence\" mean?', 'The accused is presumed innocent until proven guilty', 1, 2, 'The accused is presumed guilty until proven innocent', 'The accused is presumed innocent until proven guilty', 'The accused must prove their guilt', 'The burden of proof lies with the prosecution', 'PREBOARD1'),
(5, 'In criminal law, what is the age of criminal responsibility in the Philippines?', '15 years old', 1, 2, '12 years old', '15 years old', '18 years old', '21 years old', 'PREBOARD1'),
(6, 'Berto, with evident premeditation and treachery, killed his father.  What was the crime committed?', 'Parricide', 1, 2, 'Murder', 'Parricide', 'Homicide', 'Qualified Homicide ', 'PREBOARD2'),
(8, 'Charlie and Lea had been married for more than 6 months. They live together with the children of Lea from her first husband. Charlie had a sexual relationship with Jane, the 14-year-old daughter of  Lea. Jane loves Charlie very much. What was the crime co', 'Qualified Seduction', 1, 2, 'Simple Seduction', 'Qualified Seduction', 'Consented Abduction', 'Rape', 'PREBOARD2'),
(9, 'Prof. Jose gave a failing grade to one of his students, Lito. When the two met the following day, Lito slapped Prof. Jose in the face.  What was the crime committed by Lito?', 'Direct Assault', 1, 2, 'Corruption of Public Officials', 'Direct Assault', 'Slight Physical Injuries', 'Grave Coercion  ', 'PREBOARD2'),
(10, 'A warrant of arrest was issued against Fred for the killing of his parents.  When Patrolman Tapang tried to arrest him, Fred gave him 1 million pesos to set him free. Patrolman Tapang refrained from arresting  Fred.  What was the crime committed by Patrol', 'Qualified Bribery ', 1, 2, 'Indirect Bribery', 'Direct Bribery', 'Corruption of Public Officials', 'Qualified Bribery ', 'PREBOARD2'),
(11, 'Which of the following is the exemption to the hearsay rule made under the consciousness of an impending death?', 'Dead man statute', 1, 2, 'Parol Evidence', 'Ante mortem statement', 'Suicide note', 'Dead man statute', 'PREBOARD2'),
(12, ' It depicts the unbroken flow of command from the top to the bottom of the organizational hierarchy.', 'Chain of Command', 2, 2, 'Chain of Command', 'Span of control', 'Unity of command', ' Hierarchy of authority', 'PREBOARD1'),
(13, 'A PNP personnel with the rank of Police Lieutenant Colonel is subject to the appointing power of the ______ to the next higher rank.', 'President', 2, 2, 'President', 'DILG Secretary', 'President subject to the confirmation of the Commission on Appointments', 'Chief PNP', 'PREBOARD1'),
(14, 'Organization may be defined as arranging personnel and functions in a systematic manner designed to accommodate stated goals and objectives in the best manner possible', 'Efficient', 2, 2, 'effective', 'Excellent', 'Efficient', 'Economical', 'PREBOARD1'),
(15, 'The hierarchy of authority which is the order of ranks from the highest to the lowest levels of the organization is called', 'Scalar Chain', 2, 2, 'Scalar Chain', 'Unity of Command', 'Line of authority', 'Chain of command', 'PREBOARD1'),
(16, 'It postulates that the commander is directly responsible for any act or omission of his subordinates in relation to the performance of their official duty', 'Command responsibility', 2, 2, 'Command responsibility', 'Unity of Command', 'Delegation of Authority', 'Span of Control', 'PREBOARD1'),
(17, 'The following were authorized to conduct summary dismissal over administrative cases against uniformed PNP personnel, except.', 'IAS', 2, 2, 'IAS', 'Chief PNP', 'PNP Regional Directors', 'None of these', 'PREBOARD2'),
(18, 'The power to revoke the licenses issued to security guards is vested to', 'Chief PNP', 2, 2, 'Chief PNP', 'Secretary, DILG', 'PNP Regional Director', 'Chairman, Napolcom', 'PREBOARD2'),
(19, 'What operational support units of the PNP conduct the identification and evaluation of physical evidence related to crimes?', 'Crime Laboratory', 2, 2, 'Health service', 'Crime Laboratory', 'SOCO', 'CIDG', 'PREBOARD2'),
(20, 'This early English Police System adopted the so-called \"tithings\".', 'Frankpledge system ', 2, 2, 'Frankpledge system ', 'Metropolitan Constabulary of London', 'Parish constable system', 'Bow Street runners', 'PREBOARD2'),
(21, 'In criminal investigation before taking the sworn statement of a suspect, the investigator should inform the former of?', 'His constitutional rights', 2, 2, 'The name of the complainant and witnesses', 'His constitutional rights', 'The evidence collected against him', 'His identity as an officer in a case', 'PREBOARD2'),
(22, 'The use of one or more electrical appliances or devices that draw or consume electrical current beyond the designed capacity of the existing electrical system', 'overloading ', 3, 2, 'self-closing door ', 'jumper', 'overloading ', 'oxidizing material', 'PREBOARD1'),
(23, 'An enclosed vertical space of passage that extends from floor  to floor, as well as from the base to the top of the building is called ', 'vertical shaft ', 3, 2, 'sprinkle evidence ', 'vertical shaft ', 'flash point ', 'standpipe system', 'PREBOARD1'),
(24, 'A wall designated to prevent the spread of fire has a fire-resistance rating of not less than four hours with sufficient structural stability to remain standing even if construction on either side collapses under the fire conditions. ', 'Firewall', 3, 2, 'Wood rack  ', 'Firewall', 'Post wall', 'Firetrap', 'PREBOARD1'),
(25, 'Any act that would remove or neutralize a fire hazard. ', 'Abatement', 3, 2, 'Allotment ', 'Combustion', 'Distillation', 'Abatement', 'PREBOARD1'),
(26, 'The ____ shall be conducted as a pre-requisite to grant permits and/or licenses by local governments or other government agencies. ', 'Fire safety inspection ', 3, 2, 'Fire safety inspection ', 'Fire protection assembly ', 'Fire alerting system ', 'Fire service', 'PREBOARD1'),
(27, 'An instance that may cause fires from the heat accumulated from the rolling, sliding, or friction in machinery or between two hard surfaces, at least one of which is usually a metal is called. ', 'friction heat ', 3, 2, 'static electricity ', 'overheating of the machine ', 'friction heat ', 'heat from arching', 'PREBOARD2'),
(28, 'The minimum temperature at which a liquid forms a vapor above its surface in sufficient concentration that it can be ignited.', 'Flash point', 3, 2, 'Ignition temperature', 'Kindling temperature', 'Fire point', 'Flash point', 'PREBOARD2'),
(29, 'It is a process by which heat is transferred by movement of a heated fluid such as air or water.', 'Convection', 3, 2, 'Convection', 'Conduction', 'Radiation', 'Electrolysis', 'PREBOARD2'),
(30, 'Known as the “Fire Code of the Philippines”.', 'RA 9514', 3, 2, 'RA 6975', 'RA 9514', 'RA 9263', 'PD 1613', 'PREBOARD2'),
(31, 'Product of combustion which combustible materials and oxidizing agents react to heat.', 'Fire ', 3, 2, 'Fire ', 'Smoke', 'Fire Gases', 'Flame', 'PREBOARD2'),
(32, 'These are sets of authentic documents which will serve as a basis for comparison with other matters in question.', 'Standard Document', 4, 2, 'Questioned Document', 'Standard Document', 'Sample ', 'Exemplar ', 'PREBOARD1'),
(33, 'A type of document that bears the seals of the office issuing and the authorized signature to such document.', 'Official Document', 4, 2, 'Public Document', 'Official Document', 'Private Document', 'Commercial Document', 'PREBOARD1'),
(34, 'It refers to the group of muscles which is responsible for the formation of the upward strokes.', 'Extensor', 4, 2, 'Flexor', 'Cortex', 'Lumbrical', 'Extensor', 'PREBOARD1'),
(35, 'It refers to the sudden increase in pressure or the intermittently forcing of the pen against the paper surface with an increase in speed.', 'Pen Emphasis', 4, 2, 'Pen Pressure', 'Pen Shading', 'Pen Emphasis', 'Retouching', 'PREBOARD1'),
(36, 'It refers to the factor that relates to the condition of the writer as well as the circumstances under which the writing was prepared.', 'Writing Condition', 4, 2, 'System of Writing', 'Copy-Book Form', 'Writing Movement', 'Writing Condition', 'PREBOARD1'),
(37, 'Fingerprints of a person will be carried from the womb to the tomb.', 'Principle of Permanency', 4, 2, 'Principle of Individuality', 'Principle of Permanency', 'Principle of Infallibility', 'Principle of Fallacy', 'PREBOARD2'),
(38, 'The first conviction in the Philippines of a case that gave recognition to the science of fingerprints.', 'People vs. Medina', 4, 2, 'People vs. Medina', 'People vs. Jennings', 'Miranda vs. Arizona', 'West case', 'PREBOARD2'),
(39, 'The instruments are specially designed to permit the firearm examiner to determine the similarity and dissimilarity between two fired bullets or two fired shells.', ' Bullet Comparison Microscope', 4, 2, ' Bullet Comparison Microscope', 'Bullet Microscope', 'Bullet Comparison Microscope Microscope', 'Bullet Microscopee', 'PREBOARD2'),
(40, 'The sunken area within the rifling is observed when looking into the barrel of a firearm.', 'Grooves', 4, 2, 'Grooves', 'Lands', 'Bore', 'Pitch', 'PREBOARD2'),
(41, 'The bullet discovered at the crime scene, fired from a 38 caliber firearm, bears five lands and five grooves with a right-hand twist, suggesting that it was discharged from which type of gun?', 'Smith and Wesson', 4, 2, 'Colt', 'Browning', 'Remington', 'Smith and Wesson', 'PREBOARD2'),
(42, 'A penal method of the 19th century in which a person worked during the day and was kept in solitary confinement at night, with enforced silence at all times', ' Auburn System', 5, 2, 'Elmira System', ' Auburn System', 'Pennsylvania System', 'None of the above', 'PREBOARD1'),
(43, 'This imprisonment lasts 1 month and 1 day to 6 months.', 'Arresto Mayor', 5, 2, 'Arresto Mayor', ' Arresto Menor', 'Prision correcional', 'Prision mayor', 'PREBOARD1'),
(44, 'Justification of punishment which refers to personal vengeance.', 'Retribution', 5, 2, 'Retribution', 'Expiation', 'Deterrence', 'Rehabilitation', 'PREBOARD1'),
(45, 'What is the overall philosophy underlying therapeutic modalities in correctional administration?', 'Rehabilitation and positive change', 5, 2, 'Exclusively punitive measures', 'Rehabilitation and positive change', 'Maximum security and isolation', 'Promoting inmate aggression', 'PREBOARD1'),
(46, 'Which of the following is a common therapeutic modality used in correctional settings?', 'Cognitive-behavioral therapy', 5, 2, 'Solitary confinement', 'Excessive physical punishment', 'Cognitive-behavioral therapy', 'Restricted access to legal representation', 'PREBOARD1'),
(47, ' Which of the following statements best describes the concept of \"\r\n\"reintegration\"in therapeutic modalities?', 'Facilitating the transition of inmates back into society as law-abiding citizens', 5, 2, 'Keeping inmates isolated from society', 'Preparing inmates for lifelong incarceration', 'Facilitating the transition of inmates back into society as law-abiding citizens', 'Fostering rivalry among inmate groups', 'PREBOARD2'),
(48, 'What role can education play as a therapeutic modality in correctional administration?', 'To provide opportunities for skill development and personal growth', 5, 2, 'To increase inmates criminal knowledge', 'To reinforce positive behaviors', 'To provide opportunities for skill development and personal growth', 'To separate inmates from each other', 'PREBOARD2'),
(49, 'Restorative justice programs are a form of therapeutic modality that focuses on:', 'Rehabilitating offenders through victim-offender mediation and reconciliation', 5, 2, 'Separating inmates from society', 'Enforcing strict discipline', 'Rehabilitating offenders through victim-offender mediation and reconciliation', 'Rewarding compliant behavior', 'PREBOARD2'),
(50, 'The following are the indicators of effective Behavior Management in TCMP, except.', 'The residents were incorrigible, which made them unwanted in the community.', 5, 2, 'Understanding the different shaping tools', 'Understand the relevance of the tools.', 'The resident will display improvement.', 'The residents were incorrigible, which made them unwanted in the community.', 'PREBOARD2'),
(51, 'The following are considered Cognitive approaches, except.', 'Psychoanalytic approach', 5, 2, 'Psychoanalytic approach', 'Reality Therapy', 'Cognitive Behavior Approach', 'Acceptance and Commitment Therapy', 'PREBOARD2'),
(52, 'It is an act committed by a minor who violates the penal code of the government refers to:', 'Juvenile Delinquency', 6, 2, 'Delinquency Child', 'Juvenile', 'Delinquent', 'Juvenile Delinquency', 'PREBOARD1'),
(53, 'This refers to behavior that has brought him into repeated conflict with the law, regardless of whether he has been taken before a court or not.', 'Delinquent', 6, 2, 'Child', 'Delinquent', 'Anti-social behavior', 'Recidivist', 'PREBOARD1'),
(54, 'This doctrine emphasizes the role of the state as a \"parent\" or guardian to protect the best interests of the child.', 'Parens Patriae', 6, 2, 'Parens Patriae', 'The best interest of the child', 'Rehabilitative Model', 'Waiver to adult court', 'PREBOARD1'),
(55, 'Truancy, running away from home, and violating curfew are examples of offenses committed by:', 'Status offenders', 6, 2, 'Status offenders', 'Juvenile delinquents', 'Recidivist', 'Delinquency', 'PREBOARD1'),
(56, 'One who has internalized his conflicts and is preoccupied with his own feelings.', 'Neurotic', 6, 2, 'Neurotic', 'Social', 'Asocial', 'Accidental', 'PREBOARD1'),
(57, 'What term refers to the psychological discomfort a person experiences when their actions or beliefs contradict one another?', 'Cognitive dissonance', 6, 2, 'Cognitive dissonance', 'Confirmation bias', 'Operant conditioning', 'Social loafing', 'PREBOARD2'),
(58, 'Which of the following brain structures is primarily responsible for regulating emotions, including fear and aggression?', 'Amygdala', 6, 2, 'Hippocampus', 'Amygdala', 'Prefrontal cortex', 'Cerebellum', 'PREBOARD2'),
(59, 'What type of serial killer is motivated by a psychological need to kill and derives pleasure from the act itself?', 'Hedonistic', 6, 2, 'Mission-oriented', 'Visionary', 'Hedonistic', 'Power/control-oriented', 'PREBOARD2'),
(60, 'According to the routine activities theory, which of the following is a necessary component for a crime to occur?', 'Suitable target, motivated offender, and absence of a capable guardian', 6, 2, ' Poverty', 'Mental illness', 'Suitable target, motivated offender, and absence of a capable guardian', 'Police presence', 'PREBOARD2'),
(61, 'Which of the following is considered a biological factor that may contribute to criminal behavior?', 'Genetic predisposition', 6, 2, 'Social learning', 'Parenting style', 'Exposure to media violence', 'Genetic predisposition', 'PREBOARD2'),
(81, 'What does HTML stand for?', 'Hyper Text Markup Language', 10, 7, 'Hyperlinks and Text Markup Language', 'Hyper Text Markup Language', 'Home Tool Markup Language', 'Hyper Tell Makeup Language', 'PREBOARD1'),
(82, 'Choose the correct HTML tag for the largest heading', '<h1>', 10, 7, '<heading>', '<h6>', '<head>', '<h1>', 'PREBOARD1'),
(83, 'What is the correct HTML tag for inserting a line break?\r\n\r\n', '<br>', 10, 7, '<br>', '<lb>', '<space>', '<break>', 'PREBOARD1'),
(84, 'What is the correct HTML for creating a hyperlink?\r\n\r\n', '<a href=\"http://www.w3schools.com\">W3Schools</a>', 10, 7, '<a url=\"http://www.w3schools.com\">W3Schools.com</a>', '<a name=\"http://www.w3schools.com\">W3Schools.com</a>', '<a href=\"http://www.w3schools.com\">W3Schools</a>', '<a>http://www.w3schools.com</a>', 'PREBOARD1'),
(85, 'Which of these tags are all <table> tags?\r\n\r\n', '<table><tr><td>', 10, 7, '<table><head><tfoot>', '<table><tr><td>', '<thead><body><tr>', '<table><tr><tt>', 'PREBOARD1'),
(86, 'What is the correct HTML for inserting an image?\r\n\r\n', '<img src=\"image.gif\" alt=\"MyImage\">', 10, 7, '<img alt=\"MyImage\">image.gif</img>', '<img src=\"image.gif\" alt=\"MyImage\">', '<image src=\"image.gif\" alt=\"MyImage\">', '<img href=\"image.gif\" alt=\"MyImage\">', 'PREBOARD1'),
(87, 'How can you make a numbered list?\r\n\r\n', '<ol>', 10, 7, '<ol>', '<ul>', '<dl>', '<list>', 'PREBOARD1'),
(88, 'What is the correct HTML for making a checkbox?\r\n\r\n', '<input type=\"checkbox\">', 10, 7, '<checkbox>', '<input type=\"checkbox\">', '<check>', '<input type=\"check\">', 'PREBOARD1'),
(89, 'What does CSS stand for?\r\n\r\n', 'Cascading Style Sheets', 10, 7, 'Creative Style Sheets', 'Cascading Style Sheets', 'Computer Style Sheets', 'Colorful Style Sheets', 'PREBOARD1'),
(90, 'What is the correct HTML for referring to an external style sheet?\r\n\r\n', '<link rel=\"stylesheet\" type=\"text/css\" href=\"mystyle.css\">', 10, 7, '<stylesheet>mystyle.css</stylesheet>', '<style src=\"mystyle.css\">', '<link rel=\"stylesheet\" type=\"text/css\" href=\"mystyle.css\">', '<styl src=\"mystyle.css\">', 'PREBOARD1'),
(91, '1+1=', '2', 10, 7, '3', '2', '1', '4', 'PREBOARD2');

-- --------------------------------------------------------

--
-- Table structure for table `school_year`
--

CREATE TABLE `school_year` (
  `id` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `school_year`
--

INSERT INTO `school_year` (`id`, `description`, `status`, `user_id`, `date_created`) VALUES
(1, '2024-2025', 'Current Set', 1, '2024-12-03 02:29:53'),
(2, '2025-2026', 'Not Set', 1, '2024-12-15 06:57:46'),
(3, '2026-2027', 'Not Set', 1, '2024-12-28 01:45:58'),
(4, '2027-2028', 'Not Set', 1, '2025-01-03 06:47:15');

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

CREATE TABLE `section` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `date_entry` datetime DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `section`
--

INSERT INTO `section` (`id`, `user_id`, `description`, `date_entry`, `status`) VALUES
(1, 1, 'Section 1', '2024-12-03 02:30:32', 'Active'),
(2, 1, 'Section 2', '2024-12-03 02:30:38', 'Active'),
(3, NULL, 'Section 3', '2024-12-15 20:49:48', 'Inactive');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `about` varchar(255) DEFAULT NULL,
  `lrn_num` varchar(255) DEFAULT NULL,
  `fname` varchar(255) DEFAULT NULL,
  `lname` varchar(255) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `complete_address` varchar(255) DEFAULT NULL,
  `year_level_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `school_year_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `date_registered` datetime DEFAULT NULL,
  `logged_in` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `level` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `about`, `lrn_num`, `fname`, `lname`, `gender`, `username`, `password`, `complete_address`, `year_level_id`, `course_id`, `section_id`, `school_year_id`, `status`, `date_registered`, `logged_in`, `profile_image`, `level`) VALUES
(1, '', '202150818', 'Kate', 'Pepito', 'Female', 'KatePepito', 'Kate123', '', 1, 1, 2, 1, 'Active', '2024-12-03 02:36:48', 'NO', 'uploads/6766b9d16d4b6.jpg', 'PREBOARD2'),
(2, '', '202150218', 'Joshua', 'Pilapil', 'Male', 'JoshuaPilapil', 'Joshua12', '', 1, 1, 1, 1, 'Active', '2024-12-03 04:03:45', 'NO', 'uploads/6766b1e0c99f2.png', 'PREBOARD2'),
(6, '', '202151490', 'Regie', 'Toregossa', 'Female', 'Regie', 'Regie123', '', 1, 1, 1, 1, 'Active', '2024-12-03 08:36:28', 'NO', 'uploads/6766ba7c01cf6.jpg', 'PREBOARD2'),
(9, NULL, '202150845', 'Dave', 'Sampaga', 'Male', 'deb', 'deb', NULL, 1, 1, 2, 1, 'Inactive', '2024-12-03 09:09:49', 'NO', NULL, 'PREBOARD1'),
(14, NULL, '202150805', 'April Joy', 'Arellano', 'Female', 'AprilArellano', 'April123', NULL, 1, 1, 1, 1, 'Active', '2024-12-04 09:25:20', 'NO', NULL, 'PREBOARD1'),
(15, NULL, '202150800', 'Jumil', 'Mosenabre', 'Male', 'Jumil05', 'admin', NULL, 1, 1, 1, 1, 'Active', '2024-12-04 10:27:53', 'NO', NULL, 'PREBOARD1'),
(16, NULL, '202050893', 'Phebe Mae', 'Monding', 'Female', 'phebe', 'phebe', NULL, 1, 1, 2, 1, 'Active', '2024-12-04 10:41:46', 'NO', NULL, 'PREBOARD1'),
(17, NULL, '2021250811', 'Roxanne', 'Bonayog', 'Female', 'Roxanne', 'Roxy123', NULL, 1, 1, 1, 1, 'Active', '2024-12-04 12:44:46', 'NO', NULL, 'PREBOARD1'),
(21, NULL, '1223', '123', '123', 'Male', '1', '1', NULL, 1, 1, 1, 1, 'Inactive', '2024-12-16 11:58:46', 'NO', NULL, 'PREBOARD1'),
(22, NULL, '202450818', 'Nova Belle', 'Felias', 'Female', 'novabelle', 'nova', NULL, 1, 1, 1, 2, 'Active', '2024-12-17 10:48:44', 'NO', NULL, 'PREBOARD1'),
(23, NULL, '123456', 'num', 'num', 'Male', 'num', 'num', NULL, 1, 1, 1, NULL, 'For Approval', '2024-12-17 10:52:17', 'NO', NULL, 'PREBOARD1'),
(24, NULL, '987', 'mun', 'mun', 'Female', 'mun', 'mun', NULL, 1, 1, 2, 1, 'For Approval', '2024-12-17 11:10:34', 'NO', NULL, 'PREBOARD1'),
(25, NULL, '202250878', 'Kelly', 'Pepito', 'Female', 'kelly', 'kelly', NULL, 1, 1, 2, 2, 'Active', '2024-12-17 11:12:08', 'NO', NULL, 'PREBOARD1'),
(26, NULL, '202150918', 'Kent ', 'Pepe', 'Male', 'kr', 'kr', NULL, 1, 4, 2, 2, 'Active', '2024-12-17 11:26:30', 'NO', NULL, 'PREBOARD2'),
(27, NULL, '202150820', 'Arvin', 'Guno', 'Male', 'argunz', 'argunz', NULL, 1, 4, 1, 2, 'Inactive', '2024-12-21 03:32:29', 'NO', NULL, 'PREBOARD1'),
(28, NULL, '202450815', 'kathy', 'Pepito', 'Female', 'kathypepitowo', 'ilovetina', NULL, 1, 4, 2, 2, 'Active', '2024-12-21 04:17:08', 'NO', NULL, 'PREBOARD2'),
(29, NULL, '123456', 'lovely', 'polana', 'Female', 'lp', 'lp', NULL, 1, 4, 1, 1, 'Active', '2025-01-03 07:04:51', 'YES', NULL, 'PREBOARD1'),
(30, NULL, '202150819', 'Daisy', 'Pilapil', 'Female', 'daisy', 'daisy', NULL, 1, 1, 1, 1, 'Active', '2025-01-03 07:17:52', 'NO', NULL, 'PREBOARD1');

-- --------------------------------------------------------

--
-- Table structure for table `students_subjects`
--

CREATE TABLE `students_subjects` (
  `id` int(11) NOT NULL,
  `students_id` varchar(11) DEFAULT NULL,
  `subjects_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `level` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `students_subjects`
--

INSERT INTO `students_subjects` (`id`, `students_id`, `subjects_id`, `status`, `level`) VALUES
(3, '1', 2, 'TAKEN', 'PREBOARD1'),
(4, '1', 2, 'TAKEN', 'PREBOARD2'),
(7, '1', 4, 'TAKEN', 'PREBOARD1'),
(8, '1', 4, 'TAKEN', 'PREBOARD2'),
(9, '1', 5, 'TAKEN', 'PREBOARD1'),
(10, '1', 5, 'TAKEN', 'PREBOARD2'),
(11, '1', 6, 'TAKEN', 'PREBOARD1'),
(12, '1', 6, 'TAKEN', 'PREBOARD2'),
(13, '1', 1, 'TAKEN', 'PREBOARD1'),
(14, '1', 1, 'TAKEN', 'PREBOARD2'),
(27, '1', 3, 'TAKEN', 'PREBOARD1'),
(28, '1', 3, 'TAKEN', 'PREBOARD2'),
(29, '2', 1, 'TAKEN', 'PREBOARD1'),
(30, '2', 1, 'TAKEN', 'PREBOARD2'),
(31, '2', 2, 'TAKEN', 'PREBOARD1'),
(32, '2', 2, 'TAKEN', 'PREBOARD2'),
(33, '2', 3, 'TAKEN', 'PREBOARD1'),
(34, '2', 3, 'NOT TAKEN', 'PREBOARD2'),
(35, '2', 4, 'TAKEN', 'PREBOARD1'),
(36, '2', 4, 'NOT TAKEN', 'PREBOARD2'),
(37, '2', 5, 'TAKEN', 'PREBOARD1'),
(38, '2', 5, 'NOT TAKEN', 'PREBOARD2'),
(39, '2', 6, 'TAKEN', 'PREBOARD1'),
(40, '2', 6, 'NOT TAKEN', 'PREBOARD2'),
(44, '3', 2, 'NOT TAKEN', 'PREBOARD2'),
(66, '5', 1, 'NOT TAKEN', 'PREBOARD2'),
(71, '5', 67, 'NOT TAKEN', 'PREBOARD1'),
(72, '5', 67, 'NOT TAKEN', 'PREBOARD2'),
(73, '5', 67, 'NOT TAKEN', 'PREBOARD1'),
(74, '5', 67, 'NOT TAKEN', 'PREBOARD2'),
(75, '5', 67, 'NOT TAKEN', 'PREBOARD1'),
(76, '5', 67, 'NOT TAKEN', 'PREBOARD2'),
(77, '5', 67, 'NOT TAKEN', 'PREBOARD1'),
(78, '5', 67, 'NOT TAKEN', 'PREBOARD2'),
(79, '5', 69, 'NOT TAKEN', 'PREBOARD1'),
(80, '5', 69, 'NOT TAKEN', 'PREBOARD2'),
(81, '5', 67, 'NOT TAKEN', 'PREBOARD1'),
(82, '5', 67, 'NOT TAKEN', 'PREBOARD2'),
(83, '5', 67, 'NOT TAKEN', 'PREBOARD1'),
(84, '5', 67, 'NOT TAKEN', 'PREBOARD2'),
(85, '5', 67, 'NOT TAKEN', 'PREBOARD1'),
(86, '5', 67, 'NOT TAKEN', 'PREBOARD2'),
(87, '5', 67, 'NOT TAKEN', 'PREBOARD1'),
(88, '5', 67, 'NOT TAKEN', 'PREBOARD2'),
(89, '5', 67, 'NOT TAKEN', 'PREBOARD1'),
(90, '5', 67, 'NOT TAKEN', 'PREBOARD2'),
(91, '5', 67, 'NOT TAKEN', 'PREBOARD1'),
(92, '5', 67, 'NOT TAKEN', 'PREBOARD2'),
(93, '5', 67, 'NOT TAKEN', 'PREBOARD1'),
(94, '5', 67, 'NOT TAKEN', 'PREBOARD2'),
(95, '5', 67, 'NOT TAKEN', 'PREBOARD1'),
(96, '5', 67, 'NOT TAKEN', 'PREBOARD2'),
(97, '5', 67, 'NOT TAKEN', 'PREBOARD1'),
(98, '5', 67, 'NOT TAKEN', 'PREBOARD2'),
(99, '5', 67, 'NOT TAKEN', 'PREBOARD1'),
(100, '5', 67, 'NOT TAKEN', 'PREBOARD2'),
(101, '5', 67, 'NOT TAKEN', 'PREBOARD1'),
(102, '5', 67, 'NOT TAKEN', 'PREBOARD2'),
(103, '5', 67, 'NOT TAKEN', 'PREBOARD1'),
(104, '5', 67, 'NOT TAKEN', 'PREBOARD2'),
(105, '5', 67, 'NOT TAKEN', 'PREBOARD1'),
(106, '5', 67, 'NOT TAKEN', 'PREBOARD2'),
(107, '5', 67, 'NOT TAKEN', 'PREBOARD1'),
(108, '5', 67, 'NOT TAKEN', 'PREBOARD2'),
(109, '5', 67, 'NOT TAKEN', 'PREBOARD1'),
(110, '5', 67, 'NOT TAKEN', 'PREBOARD2'),
(111, '5', 67, 'NOT TAKEN', 'PREBOARD1'),
(112, '5', 67, 'NOT TAKEN', 'PREBOARD2'),
(113, '5', 67, 'NOT TAKEN', 'PREBOARD1'),
(114, '5', 67, 'NOT TAKEN', 'PREBOARD2'),
(115, '5', 67, 'NOT TAKEN', 'PREBOARD1'),
(116, '5', 67, 'NOT TAKEN', 'PREBOARD2'),
(143, '6', 1, 'TAKEN', 'PREBOARD1'),
(144, '6', 1, 'NOT TAKEN', 'PREBOARD2'),
(145, '6', 2, 'TAKEN', 'PREBOARD1'),
(146, '6', 2, 'NOT TAKEN', 'PREBOARD2'),
(147, '6', 3, 'TAKEN', 'PREBOARD1'),
(148, '6', 3, 'NOT TAKEN', 'PREBOARD2'),
(149, '6', 4, 'TAKEN', 'PREBOARD1'),
(150, '6', 4, 'NOT TAKEN', 'PREBOARD2'),
(151, '6', 5, 'TAKEN', 'PREBOARD1'),
(152, '6', 5, 'NOT TAKEN', 'PREBOARD2'),
(153, '6', 6, 'TAKEN', 'PREBOARD1'),
(154, '6', 6, 'NOT TAKEN', 'PREBOARD2'),
(203, '15', 1, 'TAKEN', 'PREBOARD1'),
(204, '15', 1, 'NOT TAKEN', 'PREBOARD2'),
(205, '15', 2, 'TAKEN', 'PREBOARD1'),
(206, '15', 2, 'NOT TAKEN', 'PREBOARD2'),
(207, '15', 3, 'TAKEN', 'PREBOARD1'),
(208, '15', 3, 'NOT TAKEN', 'PREBOARD2'),
(209, '15', 4, 'TAKEN', 'PREBOARD1'),
(210, '15', 4, 'NOT TAKEN', 'PREBOARD2'),
(211, '15', 5, 'TAKEN', 'PREBOARD1'),
(212, '15', 5, 'NOT TAKEN', 'PREBOARD2'),
(213, '15', 6, 'TAKEN', 'PREBOARD1'),
(214, '15', 6, 'NOT TAKEN', 'PREBOARD2'),
(215, '16', 1, 'TAKEN', 'PREBOARD1'),
(216, '16', 1, 'NOT TAKEN', 'PREBOARD2'),
(217, '16', 2, 'TAKEN', 'PREBOARD1'),
(218, '16', 2, 'NOT TAKEN', 'PREBOARD2'),
(219, '16', 3, 'TAKEN', 'PREBOARD1'),
(220, '16', 3, 'NOT TAKEN', 'PREBOARD2'),
(221, '16', 4, 'TAKEN', 'PREBOARD1'),
(222, '16', 4, 'NOT TAKEN', 'PREBOARD2'),
(223, '16', 5, 'TAKEN', 'PREBOARD1'),
(224, '16', 5, 'NOT TAKEN', 'PREBOARD2'),
(225, '16', 6, 'TAKEN', 'PREBOARD1'),
(226, '16', 6, 'NOT TAKEN', 'PREBOARD2'),
(239, '14', 1, 'NOT TAKEN', 'PREBOARD1'),
(240, '14', 1, 'NOT TAKEN', 'PREBOARD2'),
(249, '26', 10, 'TAKEN', 'PREBOARD1'),
(250, '26', 10, 'NOT TAKEN', 'PREBOARD2'),
(251, '28', 10, 'NOT TAKEN', 'PREBOARD1'),
(252, '28', 10, 'NOT TAKEN', 'PREBOARD2'),
(255, '30', 1, 'TAKEN', 'PREBOARD1'),
(256, '30', 1, 'NOT TAKEN', 'PREBOARD2'),
(259, '26', 12, 'NOT TAKEN', 'PREBOARD1'),
(260, '26', 12, 'NOT TAKEN', 'PREBOARD2'),
(261, '29', 10, 'TAKEN', 'PREBOARD1'),
(262, '29', 10, 'NOT TAKEN', 'PREBOARD2'),
(263, '29', 12, 'NOT TAKEN', 'PREBOARD1'),
(264, '29', 12, 'NOT TAKEN', 'PREBOARD2');

-- --------------------------------------------------------

--
-- Table structure for table `student_score`
--

CREATE TABLE `student_score` (
  `id` int(11) NOT NULL,
  `score` varchar(255) DEFAULT NULL,
  `total_items` varchar(255) DEFAULT NULL,
  `stud_id` int(11) DEFAULT NULL,
  `average` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `remarks2` text DEFAULT NULL,
  `sub_id` int(11) DEFAULT NULL,
  `date_accomplished` datetime DEFAULT NULL,
  `level` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `student_score`
--

INSERT INTO `student_score` (`id`, `score`, `total_items`, `stud_id`, `average`, `remarks`, `remarks2`, `sub_id`, `date_accomplished`, `level`) VALUES
(1, '5', '5', 1, '20', 'Ipadayon na!!!! yey!', 'Keep it up!!! eyyy', 1, '2024-12-03 03:45:33', 'PREBOARD1'),
(2, '5', '5', 1, '15', NULL, NULL, 2, '2024-12-03 03:46:59', 'PREBOARD1'),
(3, '5', '5', 1, '15', NULL, NULL, 4, '2024-12-03 03:47:14', 'PREBOARD1'),
(4, '4', '5', 1, '8', NULL, NULL, 5, '2024-12-03 03:47:30', 'PREBOARD1'),
(5, '5', '5', 1, '20', NULL, NULL, 6, '2024-12-03 03:48:07', 'PREBOARD1'),
(6, '5', '5', 1, '20', NULL, NULL, 3, '2024-12-03 03:48:18', 'PREBOARD1'),
(7, '0', '5', 2, '0', NULL, 'you need to study more! need effort!', 1, '2024-12-03 04:06:17', 'PREBOARD1'),
(8, '0', '5', 2, '0', NULL, NULL, 2, '2024-12-03 04:06:28', 'PREBOARD1'),
(9, '3', '5', 2, '12', NULL, NULL, 3, '2024-12-03 04:07:23', 'PREBOARD1'),
(10, '5', '5', 2, '15', NULL, NULL, 4, '2024-12-03 04:07:36', 'PREBOARD1'),
(11, '5', '5', 2, '10', NULL, NULL, 5, '2024-12-03 04:07:58', 'PREBOARD1'),
(12, '5', '5', 2, '20', NULL, NULL, 6, '2024-12-03 04:08:08', 'PREBOARD1'),
(13, '5', '5', 1, '20', 'Goodluck sa Board Exam!! kaya na nimo goods', 'GOODLUCK SA BOARD EXAM!! goods', 1, '2024-12-03 04:12:23', 'PREBOARD2'),
(14, '2', '5', 1, '6', NULL, NULL, 2, '2024-12-03 04:12:44', 'PREBOARD2'),
(15, '4', '5', 1, '12', NULL, NULL, 4, '2024-12-03 04:13:09', 'PREBOARD2'),
(16, '5', '5', 1, '10', NULL, NULL, 5, '2024-12-03 04:13:26', 'PREBOARD2'),
(17, '4', '5', 1, '16', NULL, NULL, 6, '2024-12-03 04:13:52', 'PREBOARD2'),
(18, '4', '5', 1, '16', NULL, NULL, 3, '2024-12-03 04:14:14', 'PREBOARD2'),
(19, '5', '5', 6, '20', NULL, NULL, 1, '2024-12-03 08:38:07', 'PREBOARD1'),
(20, '3', '5', 6, '9', NULL, NULL, 2, '2024-12-03 08:38:21', 'PREBOARD1'),
(21, '4', '5', 6, '16', NULL, NULL, 3, '2024-12-03 08:38:34', 'PREBOARD1'),
(22, '5', '5', 6, '15', NULL, NULL, 4, '2024-12-03 08:38:47', 'PREBOARD1'),
(23, '1', '5', 6, '2', NULL, NULL, 5, '2024-12-03 08:38:57', 'PREBOARD1'),
(24, '2', '5', 6, '8', NULL, NULL, 6, '2024-12-03 08:39:08', 'PREBOARD1'),
(25, '1', '5', 15, '4', NULL, NULL, 1, '2024-12-04 10:30:07', 'PREBOARD1'),
(26, '2', '5', 15, '6', NULL, NULL, 2, '2024-12-04 10:32:25', 'PREBOARD1'),
(27, '1', '5', 15, '4', NULL, NULL, 3, '2024-12-04 10:34:07', 'PREBOARD1'),
(28, '3', '5', 15, '9', NULL, NULL, 4, '2024-12-04 10:35:15', 'PREBOARD1'),
(29, '4', '5', 15, '8', NULL, NULL, 5, '2024-12-04 10:35:42', 'PREBOARD1'),
(30, '0', '5', 15, '0', NULL, NULL, 6, '2024-12-04 10:36:09', 'PREBOARD1'),
(31, '3', '5', 16, '12', 'Great', NULL, 1, '2024-12-04 10:44:10', 'PREBOARD1'),
(32, '3', '5', 16, '9', NULL, NULL, 2, '2024-12-04 10:45:52', 'PREBOARD1'),
(33, '4', '5', 16, '16', NULL, NULL, 3, '2024-12-04 10:47:01', 'PREBOARD1'),
(34, '2', '5', 16, '6', NULL, NULL, 4, '2024-12-04 10:48:30', 'PREBOARD1'),
(35, '4', '5', 16, '8', NULL, NULL, 5, '2024-12-04 10:49:50', 'PREBOARD1'),
(36, '3', '5', 16, '12', NULL, NULL, 6, '2024-12-04 10:51:16', 'PREBOARD1'),
(37, '3', '5', 2, '12', 'Study more!', NULL, 1, '2024-12-21 09:23:20', 'PREBOARD2'),
(38, '2', '5', 2, '6', NULL, NULL, 2, '2024-12-21 09:28:56', 'PREBOARD2'),
(39, '10', '10', 26, '20', 'GREAT JOB KEEP IT UP!!!', 'good job!', 10, '2024-12-28 04:27:33', 'PREBOARD1'),
(40, '3', '5', 30, '12', 'Ipadayon imong pag ka maayo', 'Study hard!!!!!', 1, '2025-01-03 07:21:49', 'PREBOARD1'),
(41, '1', '10', 29, '2', NULL, NULL, 10, '2025-01-09 09:40:57', 'PREBOARD1');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `year_level_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `school_year_id` int(11) DEFAULT NULL,
  `date_entry` datetime DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `code`, `description`, `year_level_id`, `course_id`, `school_year_id`, `date_entry`, `status`) VALUES
(1, 'CRIM01', 'Criminal Jurisprudence and Procedure', 1, 1, 1, '2024-12-03 02:31:03', 'Active'),
(2, 'CRIM02', 'Law Enforcement Administration ', 1, 1, 1, '2024-12-03 02:31:19', 'Active'),
(3, 'CRIM03', 'Crime Detection and Investigation ', 1, 1, 1, '2024-12-03 02:31:30', 'Active'),
(4, 'CRIM04', 'Criminalistics (Forensic Science) ', 1, 1, 1, '2024-12-03 02:31:51', 'Active'),
(5, 'CRIM05', 'Correctional Administration ', 1, 1, 1, '2024-12-03 02:32:03', 'Active'),
(6, 'CRIM06', 'Criminology ', 1, 1, 1, '2024-12-03 02:32:17', 'Active'),
(7, 'MATH', 'MATHEMATICS', 1, 1, 1, '2024-12-03 03:56:54', 'Inactive'),
(10, 'IT01', 'Web Development', 1, 4, 2, '2024-12-21 15:24:42', 'Active'),
(11, 'IT02', 'Programming', 1, 4, 1, '2024-12-23 20:40:50', 'Inactive'),
(12, 'IT03', 'Networking', 1, 4, 1, '2024-12-23 20:42:55', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `subjects_timer`
--

CREATE TABLE `subjects_timer` (
  `id` int(11) NOT NULL,
  `subjects_id` int(11) NOT NULL,
  `timer` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `subjects_timer`
--

INSERT INTO `subjects_timer` (`id`, `subjects_id`, `timer`) VALUES
(1, 1, '10'),
(2, 2, '10'),
(3, 3, '5'),
(4, 4, '30'),
(5, 5, '5'),
(6, 6, '5'),
(7, 7, '10'),
(8, 8, '10'),
(9, 10, '90'),
(10, 11, '10'),
(11, 12, '10');

-- --------------------------------------------------------

--
-- Table structure for table `subject_percent`
--

CREATE TABLE `subject_percent` (
  `id` int(11) NOT NULL,
  `sub_id` int(11) DEFAULT NULL,
  `percent` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `subject_percent`
--

INSERT INTO `subject_percent` (`id`, `sub_id`, `percent`) VALUES
(1, 1, '20'),
(2, 2, '15'),
(3, 3, '20'),
(4, 4, '15'),
(5, 5, '10'),
(6, 6, '20'),
(7, 7, '100'),
(8, 8, '100'),
(9, 10, '20'),
(10, 11, '100'),
(11, 12, '30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `fname` varchar(255) DEFAULT NULL,
  `lname` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `logged_in` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `type`, `status`, `fname`, `lname`, `profile_image`, `date_created`, `logged_in`) VALUES
(1, 'admin', 'admin', 'ADMIN', 'Active', 'EDP', 'Personnel', '../uploads/6777bf0a41cad.jpg', '2024-11-21 09:07:16', 'NO'),
(2, 'JissrelAcabo', 'Acabo12', 'REVIEWER', 'Active', 'Jissrell', 'Acabo', '../uploads/67774a2ec09bd.jpg', '2024-12-03 02:32:48', 'YES'),
(3, 'JunVillarmia', 'Jun123', 'DEAN', 'Active', 'Jun', 'Villarmia', '../uploads/67774a0e51255.png', '2024-12-15 06:16:25', 'YES'),
(4, 'kk', 'kk', 'DEAN', 'Inactive', 'korek', 'a', NULL, '2024-12-15 06:28:41', 'NO'),
(5, 'DaisaGupit', 'Daisa123', 'DEAN', 'Active', 'Daisa', 'Gupit', '../uploads/676fae3bab708.png', '2024-12-21 02:27:45', 'YES'),
(6, 'A', 'A', 'DEAN', 'Active', 'A', 'B', NULL, '2024-12-23 10:52:32', 'NO'),
(7, 'MarlonT', 'Marlon', 'REVIEWER', 'Active', 'Marlon juhn', 'Timogan', '../uploads/676fb4df97218.jpg', '2024-12-24 01:49:04', 'YES'),
(8, 'ReginaldG', 'Reginald123', 'REVIEWER', 'Active', 'Reginald Ryan', 'Gosela', NULL, '2024-12-24 05:21:06', 'YES'),
(9, 'IvanH', 'Ivan', 'REVIEWER', 'Active', 'Ivan Allen', 'Honrada', NULL, '2024-12-24 05:21:28', 'NO'),
(10, 'JessieM', 'jessie', 'REVIEWER', 'Inactive', 'Jessie', 'Mahinay', NULL, '2024-12-25 05:06:21', 'NO'),
(12, 'ro', 'ro', 'REVIEWER', 'Active', 'Rea mie', 'Omas-as', NULL, '2025-01-03 06:45:42', 'YES');

-- --------------------------------------------------------

--
-- Table structure for table `year_level`
--

CREATE TABLE `year_level` (
  `id` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `date_entry` datetime DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `year_level`
--

INSERT INTO `year_level` (`id`, `description`, `user_id`, `date_entry`, `status`) VALUES
(1, '4th year', 1, '2024-12-03 02:30:11', 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `faculty_subjects`
--
ALTER TABLE `faculty_subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faculty_id` (`faculty_id`),
  ADD KEY `subject_id` (`subjects_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `school_year_id` (`school_year_id`);

--
-- Indexes for table `question_answer`
--
ALTER TABLE `question_answer`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `school_year`
--
ALTER TABLE `school_year`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `section`
--
ALTER TABLE `section`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `students_subjects`
--
ALTER TABLE `students_subjects`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `student_score`
--
ALTER TABLE `student_score`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `subjects_timer`
--
ALTER TABLE `subjects_timer`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `subject_percent`
--
ALTER TABLE `subject_percent`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `year_level`
--
ALTER TABLE `year_level`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `faculty_subjects`
--
ALTER TABLE `faculty_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `question_answer`
--
ALTER TABLE `question_answer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `school_year`
--
ALTER TABLE `school_year`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `section`
--
ALTER TABLE `section`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `students_subjects`
--
ALTER TABLE `students_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=265;

--
-- AUTO_INCREMENT for table `student_score`
--
ALTER TABLE `student_score`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `subjects_timer`
--
ALTER TABLE `subjects_timer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `subject_percent`
--
ALTER TABLE `subject_percent`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `year_level`
--
ALTER TABLE `year_level`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `faculty_subjects`
--
ALTER TABLE `faculty_subjects`
  ADD CONSTRAINT `faculty_subjects_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `faculty_subjects_ibfk_2` FOREIGN KEY (`subjects_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `faculty_subjects_ibfk_3` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`),
  ADD CONSTRAINT `faculty_subjects_ibfk_4` FOREIGN KEY (`school_year_id`) REFERENCES `school_year` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
