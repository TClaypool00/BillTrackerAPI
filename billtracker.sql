SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `billtracker` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `billtracker`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `delComment` (IN `comment_id` INT)   BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
    	ROLLBACK;
    END;

	START TRANSACTION;
		DELETE FROM replies WHERE CommentId = comment_id;
		DELETE FROM comments WHERE CommentId = comment_id;
    
    COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `delMisc` (IN `id` INT(11))   DELETE FROM miscellaneous where MiscellaneousId = id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `delReply` (IN `reply_id` INT)   DELETE FROM replies WHERE ReplyId = reply_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `delSuggestion` (IN `suggestion_id` INT(11))   BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
    	ROLLBACK;
    END;
    
    START TRANSACTION;
   	DELETE FROM suggestions WHERE SuggestionId = suggestion_id;
    COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insBill` (IN `bill_name` VARCHAR(255), IN `amount_due` DECIMAL(15,2), IN `company_id` INT(11), IN `date_due` DATE, IN `return_object` BOOLEAN)   BEGIN
	DECLARE billId INT;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
    	ROLLBACK;
    END;
	
    START TRANSACTION;
    	INSERT INTO bills (BillName, AmountDue, CompanyId)
    	VALUES (bill_name, amount_due, company_id);

    	SET billId = LAST_INSERT_ID();
    
    	INSERT INTO paymenthistory (ExpenseId, TypeId, DateDue)
    	VALUES (billId, 1, date_due);
        
       	IF return_object THEN
        	SELECT BillId, UserId, CompanyName, FirstName, LastName FROM vwbills WHERE BillId = billId;
        END IF;
        
        COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insComment` (IN `comment_body` VARCHAR(255), IN `post_id` INT, IN `user_id` INT)   INSERT INTO comments (CommentBody, PostId, UserId)
VALUES (comment_body, post_id, user_id)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insCompany` (IN `company_name` VARCHAR(255), IN `user_id` INT(11))   INSERT INTO companies (CompanyName, UserId)
VALUES (company_name, user_id)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insError` (IN `error_message` VARCHAR(255), IN `err_code` INT(11), IN `err_line` INT(11), IN `stack_trace` TEXT, IN `user_id` INT(11))   BEGIN
	DECLARE error_id INT;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
    	SELECT 'An error happend' as Message;
    	ROLLBACK;
    END;
    
    START TRANSACTION;
    	IF EXISTS(SELECT * FROM error WHERE ErrorCode = err_code) THEN
        	SET error_id = (SELECT ErrorId FROM error WHERE ErrorCode = err_code);
        ELSE        	
        	INSERT INTO error(ErrorMessage, ErrorCode, ErrorLine, StackTrace)
            VALUES (error_message, err_code, err_line, stack_trace);

            SET error_id = LAST_INSERT_ID();        	
        END IF;
        
        IF NOT (EXISTS(SELECT UserId FROM usererror WHERE ErrorId = error_id AND UserId = user_id)) THEN
	        INSERT INTO usererror (ErrorId, UserId)
        	VALUES (error_id, user_id);
        END IF;
        COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insLoan` (IN `loan_name` VARCHAR(255), IN `monthly_amt_due` DECIMAL(11,2), IN `total_loan_amt` DECIMAL(11,2), IN `remaining_amt` DECIMAL(11,2), IN `company_id` INT(11), IN `date_due` DATE)   BEGIN
	DECLARE loanId INT;

	INSERT INTO loans (LoanName, MonthlyAmountDue, TotalAmountDue, RemainingAmount, CompanyId)
	VALUES (loan_name, monthly_amt_due, total_loan_amt, remaining_amt, company_id);
    
    SET loanId = LAST_INSERT_ID();
    
    INSERT INTO paymenthistory (ExpenseId, TypeId, DateDue)
    VALUES (loanId, 2, date_due);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insMisc` (IN `name` VARCHAR(255), IN `amount` DECIMAL(11,2), IN `company_id` INT(11))   BEGIN
	DECLARE miscId INT;

	INSERT INTO miscellaneous (Name, Amount, CompanyId)
	VALUES (name, amount, company_id);
    
    SET miscId = LAST_INSERT_ID();
    
    INSERT INTO paymenthistory (ExpenseId, TypeId)
    VALUES (miscId, 4);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insPaymentHistory` (IN `expense_id` INT(11), IN `type_id` INT(11))   INSERT INTO paymenthistory (ExpenseId, TypeId)
VALUES (expense_id, type_id)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insPost` (IN `post_body` VARCHAR(255), IN `user_id` INT)   BEGIN
	DECLARE post_id INT;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
    	ROLLBACK;
    END;
    
	START TRANSACTION;
        INSERT INTO posts (PostBody, UserId)
        VALUES (post_body, user_id);
        
        SET post_id = LAST_INSERT_ID();
        
        SELECT PostId, DatePosted, IsEdited FROM posts WHERE PostId = post_id;
    
    COMMIT;
    
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insSub` (IN `name` VARCHAR(255), IN `amount_due` DECIMAL(11,2), IN `due_date` DATE, IN `company_id` INT)   BEGIN
	DECLARE subId INT;

	INSERT INTO subscriptions (Name, AmountDue, CompanyId)
    VALUES (name, amount_due, company_id);
    
    SET subId = LAST_INSERT_ID();
    
    INSERT INTO paymenthistory (ExpenseId, TypeId, DateDue)
    VALUES (subId, 3, due_date);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insSuggestion` (IN `header` VARCHAR(255), IN `body` TEXT, IN `user_id` INT)   BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
    	ROLLBACK;
    END;
    
   	START TRANSACTION;
    INSERT INTO suggestions (SuggestHeader, SuggestBody, UserId)
    VALUES (header, body, user_id);
    
    COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insUser` (IN `first_name` VARCHAR(255), IN `last_name` VARCHAR(255), IN `email` VARCHAR(255), IN `password` VARCHAR(255), IN `phone_num` VARCHAR(10))   BEGIN
	DECLARE user_id INT;
	INSERT INTO users (FirstName, LastName, Email, Password, IsAdmin, PhoneNumber)
    VALUES (first_name, last_name, email, password, FALSE, phone_num);
    
    SET user_id = LAST_INSERT_ID();
    
    INSERT INTO userprofile (UserId)
    VALUES (user_id);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `selCompanyDropDown` (IN `user_id` INT(11))   SELECT c.CompanyId, c.CompanyName FROM companies c
WHERE c.UserId = user_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updApproveDenySuggestion` (IN `suggestion_id` INT, IN `approve_deny_id` INT, IN `deny_reason` TEXT, IN `user_id` INT)   BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
    	ROLLBACK;
    END;
    
    START TRANSACTION;
    
    UPDATE suggestions
    SET SuggestionOption = approve_deny_id, DenyReason = deny_reason, ApproveDenyBy = user_id
    WHERE SuggestionId = suggestion_id;
    
    COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateProfile` (IN `monthly_salary` DOUBLE(11,2), IN `budget` DOUBLE(11,2), IN `saving` DOUBLE(11,2), IN `user_id` INT(11))   UPDATE userprofile
SET MonthlySalary = monthly_salary, Budget = budget, Savings = saving
WHERE UserId = user_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updBill` (IN `bill_name` VARCHAR(255), IN `amount_due` DECIMAL(11,2), IN `is_active` BOOLEAN, IN `bill_id` INT(11))   BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
    	ROLLBACK;
    END;

	START TRANSACTION;
	UPDATE bills
	SET BillName = bill_name, AmountDue = amount_due, IsActive = is_active
	WHERE BillId = bill_id;
    
    SELECT UserId, FirstName, LastName FROM vwbills WHERE BillId = bill_id;
    
    COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updComment` (IN `comment_body` VARCHAR(255), IN `comment_id` INT)   UPDATE comments
SET CommentBody = comment_body, IsEdited = TRUE
WHERE CommentId = comment_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updCompanyName` (IN `company_name` VARCHAR(255), IN `company_id` INT(11))   UPDATE companies
SET CompanyName = company_name
WHERE CompanyId = company_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updLoan` (IN `loan_name` VARCHAR(255), IN `is_active` BOOLEAN, IN `monthly_amt_due` DECIMAL(11,2), IN `total_amt_due` DECIMAL(11,2), IN `remaining_amt_due` DECIMAL(11,2), IN `loan_id` INT(11))   UPDATE loans
SET IsActive = is_active, MonthlyAmountDue = monthly_amt_due, TotalAmountDue = total_amt_due, RemainingAmount = remaining_amt_due
WHERE LoanId = loan_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updMisc` (IN `name` VARCHAR(255), IN `amount` DECIMAL(11,2), IN `company_id` INT(11), IN `id` INT(11))   UPDATE miscellaneous
SET Name = name, Amount = amount, CompanyId = company_id
WHERE MiscellaneousId = id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updPayExpense` (IN `expense_id` INT(11), IN `type_id` INT(11))   BEGIN
    UPDATE paymenthistory
    SET IsPaid = TRUE, DatePaid = NOW()
    WHERE ExpenseId = expense_id AND TypeId = type_id
    AND MONTH(DateDue) = MONTH(NOW());

    IF type_id = 2 THEN
      UPDATE loans
        SET RemainingAmount = RemainingAmount - amount
        WHERE LoanId = expense_id;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updPost` (IN `post_body` VARCHAR(255), IN `post_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
    	ROLLBACK;
    END;
    
	START TRANSACTION;
        UPDATE posts
		SET PostBody = post_body, IsEdited = TRUE
		WHERE PostId = post_id;
        
        SELECT DatePosted, IsEdited FROM posts WHERE PostId = post_id;
    
    COMMIT;
    
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updReply` (IN `reply_body` VARCHAR(255), IN `reply_id` INT)   UPDATE replies
SET ReplyBody = reply_body, IsEdited = TRUE
WHERE ReplyId = reply_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updSub` (IN `name` VARCHAR(255), IN `amount_due` DECIMAL(11,2), IN `due_date` DATE, IN `is_active` BOOLEAN, IN `company_id` INT, IN `id` INT)   BEGIN
    UPDATE subscriptions
    SET Name = name, AmountDue = amount_due, IsActive = is_active, CompanyId = company_id
    WHERE SubscriptionId = id;

    UPDATE paymenthistory
    SET DateDue = due_date
    WHERE ExpenseId = id AND TypeId = 3 AND (MONTH(DateDue) = MONTH(NOW()) AND YEAR(DateDue) = YEAR(NOW()));
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updSuggestion` (IN `suggestion_id` INT, IN `header` INT, IN `body` INT)   BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
    	ROLLBACK;
    END;
    
    START TRANSACTION;
    UPDATE suggestions
    SET SuggestHeader = header, SuggestBody = body
    WHERE SuggestionId = suggestion_id;
    
    COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updUser` (IN `first_name` VARCHAR(255), IN `last_name` VARCHAR(255), IN `email` VARCHAR(255), IN `phone_num` VARCHAR(10), IN `user_id` INT(11))   UPDATE users
SET FirstName = first_name, LastName = last_name, Email = email, PhoneNumber = phone_num
WHERE UserId = user_id$$

DELIMITER ;

CREATE TABLE `bills` (
  `BillId` int(11) NOT NULL,
  `BillName` varchar(255) NOT NULL,
  `AmountDue` decimal(15,2) NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `CompanyId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `comments` (
  `CommentId` int(11) NOT NULL,
  `CommentBody` varchar(255) NOT NULL,
  `DatePosted` datetime NOT NULL DEFAULT current_timestamp(),
  `IsEdited` tinyint(1) NOT NULL DEFAULT 0,
  `PostId` int(11) NOT NULL,
  `UserId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `companies` (
  `CompanyId` int(11) NOT NULL,
  `CompanyName` varchar(255) NOT NULL,
  `UserId` int(11) NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `error` (
  `ErrorId` int(11) NOT NULL,
  `ErrorMessage` varchar(255) NOT NULL,
  `ErrorCode` int(11) NOT NULL,
  `ErrorLine` int(11) NOT NULL,
  `StackTrace` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `loans` (
  `LoanId` int(11) NOT NULL,
  `LoanName` varchar(255) NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `MonthlyAmountDue` decimal(11,2) NOT NULL,
  `TotalAmountDue` decimal(11,2) NOT NULL,
  `RemainingAmount` decimal(11,2) NOT NULL,
  `CompanyId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `miscellaneous` (
  `MiscellaneousId` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Amount` decimal(11,2) NOT NULL,
  `DateAdded` date NOT NULL DEFAULT current_timestamp(),
  `CompanyId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `paymenthistory` (
  `PaymentId` int(11) NOT NULL,
  `ExpenseId` int(11) NOT NULL,
  `TypeId` int(11) NOT NULL,
  `IsPaid` tinyint(1) NOT NULL DEFAULT 0,
  `DateDue` date NOT NULL,
  `DatePaid` date DEFAULT NULL,
  `IsLate` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `posts` (
  `PostId` int(11) NOT NULL,
  `PostBody` varchar(255) NOT NULL,
  `DatePosted` datetime NOT NULL DEFAULT current_timestamp(),
  `IsEdited` tinyint(1) NOT NULL DEFAULT 0,
  `UserId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `replies` (
  `ReplyId` int(11) NOT NULL,
  `ReplyBody` varchar(255) NOT NULL,
  `DatePosted` datetime NOT NULL DEFAULT current_timestamp(),
  `IsEdited` tinyint(1) NOT NULL DEFAULT 0,
  `CommentId` int(11) NOT NULL,
  `UserId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `subscriptions` (
  `SubscriptionId` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `AmountDue` decimal(11,2) NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `CompanyId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `suggestions` (
  `SuggestionId` int(11) NOT NULL,
  `SuggestHeader` varchar(255) NOT NULL,
  `SuggestBody` text NOT NULL,
  `DateSubmitted` datetime NOT NULL DEFAULT current_timestamp(),
  `UserId` int(11) NOT NULL,
  `SuggestionOption` int(11) DEFAULT NULL,
  `DenyReason` text DEFAULT NULL,
  `ApproveDenyBy` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `types` (
  `TypeId` int(11) NOT NULL,
  `TypeName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `types` (`TypeId`, `TypeName`) VALUES
(1, 'Bill'),
(2, 'Loan'),
(3, 'Subscriptions'),
(4, 'Miscellaneous');

CREATE TABLE `usererror` (
  `UserId` int(11) NOT NULL,
  `ErrorId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `userprofile` (
  `ProfileId` int(11) NOT NULL,
  `MonthlySalary` decimal(11,2) NOT NULL DEFAULT 0.00,
  `Budget` decimal(11,2) NOT NULL DEFAULT 0.00,
  `Savings` decimal(11,2) NOT NULL DEFAULT 0.00,
  `UserId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `users` (
  `UserId` int(11) NOT NULL,
  `FirstName` varchar(255) NOT NULL,
  `LastName` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `PhoneNumber` varchar(10) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `IsAdmin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE `vwbills` (
`BillId` int(11)
,`BillName` varchar(255)
,`AmountDue` decimal(15,2)
,`IsActive` tinyint(1)
,`DateDue` date
,`DatePaid` date
,`IsPaid` tinyint(1)
,`IsLate` tinyint(1)
,`CompanyId` int(11)
,`CompanyName` varchar(255)
,`UserId` int(11)
,`FirstName` varchar(255)
,`LastName` varchar(255)
);
CREATE TABLE `vwcompanies` (
`CompanyId` int(11)
,`CompanyName` varchar(255)
,`UserId` int(11)
,`IsActive` tinyint(1)
,`FirstName` varchar(255)
,`LastName` varchar(255)
);
CREATE TABLE `vwloans` (
`LoanId` int(11)
,`LoanName` varchar(255)
,`IsActive` tinyint(1)
,`MonthlyAmountDue` decimal(11,2)
,`TotalAmountDue` decimal(11,2)
,`RemainingAmount` decimal(11,2)
,`DateDue` date
,`DatePaid` date
,`IsPaid` tinyint(1)
,`IsLate` tinyint(1)
,`CompanyId` int(11)
,`CompanyName` varchar(255)
,`UserId` int(11)
,`FirstName` varchar(255)
,`LastName` varchar(255)
);
CREATE TABLE `vwmiscellaneous` (
`MiscellaneousId` int(11)
,`Name` varchar(255)
,`Amount` decimal(11,2)
,`DateAdded` date
,`CompanyId` int(11)
,`CompanyName` varchar(255)
,`UserId` int(11)
,`FirstName` varchar(255)
,`LastName` varchar(255)
);
CREATE TABLE `vwposts` (
`PostId` int(11)
,`PostBody` varchar(255)
,`DatePosted` datetime
,`IsEdited` tinyint(1)
,`UserId` int(11)
,`FirstName` varchar(255)
,`LastName` varchar(255)
);
CREATE TABLE `vwsubscriptions` (
`SubscriptionId` int(11)
,`Name` varchar(255)
,`AmountDue` decimal(11,2)
,`IsActive` tinyint(1)
,`DateDue` date
,`DatePaid` date
,`IsPaid` tinyint(1)
,`IsLate` tinyint(1)
,`CompanyId` int(11)
,`CompanyName` varchar(255)
,`UserId` int(11)
,`FirstName` varchar(255)
,`LastName` varchar(255)
);
CREATE TABLE `vwsuggestions` (
`SuggestionId` int(11)
,`SuggestHeader` varchar(255)
,`SuggestBody` text
,`DateSubmitted` datetime
,`AuthorId` int(11)
,`AuthorFirstName` varchar(255)
,`AuthorLastName` varchar(255)
,`SuggestionOption` int(11)
,`WaitingOption` varchar(255)
,`DenyReason` text
,`ApproveDenyBy` int(11)
,`FirstName` varchar(255)
,`LastName` varchar(255)
);
CREATE TABLE `vwusers` (
`UserId` int(11)
,`FirstName` varchar(255)
,`LastName` varchar(255)
,`Email` varchar(255)
,`PhoneNumber` varchar(10)
,`Password` varchar(255)
,`IsAdmin` tinyint(1)
,`ProfileId` int(11)
,`MonthlySalary` decimal(11,2)
,`Budget` decimal(11,2)
,`Savings` decimal(11,2)
);

CREATE TABLE `watingoptions` (
  `OptionId` int(11) NOT NULL,
  `WaitingOption` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `watingoptions` (`OptionId`, `WaitingOption`) VALUES
(1, 'Approve'),
(2, 'Deny');
DROP TABLE IF EXISTS `vwbills`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwbills`  AS SELECT `b`.`BillId` AS `BillId`, `b`.`BillName` AS `BillName`, `b`.`AmountDue` AS `AmountDue`, `b`.`IsActive` AS `IsActive`, `h`.`DateDue` AS `DateDue`, `h`.`DatePaid` AS `DatePaid`, `h`.`IsPaid` AS `IsPaid`, `h`.`IsLate` AS `IsLate`, `b`.`CompanyId` AS `CompanyId`, `c`.`CompanyName` AS `CompanyName`, `c`.`UserId` AS `UserId`, `u`.`FirstName` AS `FirstName`, `u`.`LastName` AS `LastName` FROM (((`bills` `b` join `companies` `c` on(`c`.`CompanyId` = `b`.`CompanyId`)) join `users` `u` on(`u`.`UserId` = `c`.`UserId`)) join `paymenthistory` `h` on(`h`.`ExpenseId` = `b`.`BillId` and `h`.`TypeId` = 1 and month(`h`.`DateDue`) = month(current_timestamp()) and year(`h`.`DateDue`) = year(current_timestamp())))  ;
DROP TABLE IF EXISTS `vwcompanies`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwcompanies`  AS SELECT `c`.`CompanyId` AS `CompanyId`, `c`.`CompanyName` AS `CompanyName`, `c`.`UserId` AS `UserId`, `c`.`IsActive` AS `IsActive`, `u`.`FirstName` AS `FirstName`, `u`.`LastName` AS `LastName` FROM (`companies` `c` join `users` `u` on(`u`.`UserId` = `c`.`UserId`))  ;
DROP TABLE IF EXISTS `vwloans`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwloans`  AS SELECT `l`.`LoanId` AS `LoanId`, `l`.`LoanName` AS `LoanName`, `l`.`IsActive` AS `IsActive`, `l`.`MonthlyAmountDue` AS `MonthlyAmountDue`, `l`.`TotalAmountDue` AS `TotalAmountDue`, `l`.`RemainingAmount` AS `RemainingAmount`, `h`.`DateDue` AS `DateDue`, `h`.`DatePaid` AS `DatePaid`, `h`.`IsPaid` AS `IsPaid`, `h`.`IsLate` AS `IsLate`, `l`.`CompanyId` AS `CompanyId`, `c`.`CompanyName` AS `CompanyName`, `c`.`UserId` AS `UserId`, `u`.`FirstName` AS `FirstName`, `u`.`LastName` AS `LastName` FROM (((`loans` `l` join `companies` `c` on(`c`.`CompanyId` = `l`.`CompanyId`)) join `users` `u` on(`u`.`UserId` = `c`.`UserId`)) join `paymenthistory` `h` on(`h`.`ExpenseId` = `l`.`LoanId` and `h`.`TypeId` = 2 and month(`h`.`DateDue`) = month(current_timestamp()) and year(`h`.`DateDue`) = year(current_timestamp())))  ;
DROP TABLE IF EXISTS `vwmiscellaneous`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwmiscellaneous`  AS SELECT `m`.`MiscellaneousId` AS `MiscellaneousId`, `m`.`Name` AS `Name`, `m`.`Amount` AS `Amount`, `m`.`DateAdded` AS `DateAdded`, `m`.`CompanyId` AS `CompanyId`, `c`.`CompanyName` AS `CompanyName`, `u`.`UserId` AS `UserId`, `u`.`FirstName` AS `FirstName`, `u`.`LastName` AS `LastName` FROM ((`miscellaneous` `m` join `companies` `c` on(`c`.`CompanyId` = `m`.`CompanyId`)) join `users` `u` on(`u`.`UserId` = `c`.`UserId`))  ;
DROP TABLE IF EXISTS `vwposts`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwposts`  AS SELECT `p`.`PostId` AS `PostId`, `p`.`PostBody` AS `PostBody`, `p`.`DatePosted` AS `DatePosted`, `p`.`IsEdited` AS `IsEdited`, `p`.`UserId` AS `UserId`, `u`.`FirstName` AS `FirstName`, `u`.`LastName` AS `LastName` FROM (`posts` `p` join `users` `u` on(`p`.`UserId` = `u`.`UserId`))  ;
DROP TABLE IF EXISTS `vwsubscriptions`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwsubscriptions`  AS SELECT `s`.`SubscriptionId` AS `SubscriptionId`, `s`.`Name` AS `Name`, `s`.`AmountDue` AS `AmountDue`, `s`.`IsActive` AS `IsActive`, `h`.`DateDue` AS `DateDue`, `h`.`DatePaid` AS `DatePaid`, `h`.`IsPaid` AS `IsPaid`, `h`.`IsLate` AS `IsLate`, `s`.`CompanyId` AS `CompanyId`, `c`.`CompanyName` AS `CompanyName`, `c`.`UserId` AS `UserId`, `u`.`FirstName` AS `FirstName`, `u`.`LastName` AS `LastName` FROM (((`subscriptions` `s` join `companies` `c` on(`c`.`CompanyId` = `s`.`CompanyId`)) join `users` `u` on(`u`.`UserId` = `c`.`UserId`)) join `paymenthistory` `h` on(`h`.`ExpenseId` = `s`.`SubscriptionId` and `h`.`TypeId` = 3 and month(`h`.`DateDue`) = month(current_timestamp()) and year(`h`.`DateDue`) = year(current_timestamp())))  ;
DROP TABLE IF EXISTS `vwsuggestions`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwsuggestions`  AS SELECT `s`.`SuggestionId` AS `SuggestionId`, `s`.`SuggestHeader` AS `SuggestHeader`, `s`.`SuggestBody` AS `SuggestBody`, `s`.`DateSubmitted` AS `DateSubmitted`, `s`.`UserId` AS `AuthorId`, `u`.`FirstName` AS `AuthorFirstName`, `u`.`LastName` AS `AuthorLastName`, `s`.`SuggestionOption` AS `SuggestionOption`, `o`.`WaitingOption` AS `WaitingOption`, `s`.`DenyReason` AS `DenyReason`, `s`.`ApproveDenyBy` AS `ApproveDenyBy`, `ad`.`FirstName` AS `FirstName`, `ad`.`LastName` AS `LastName` FROM (((`suggestions` `s` join `users` `u` on(`u`.`UserId` = `s`.`UserId`)) left join `watingoptions` `o` on(`o`.`OptionId` = `s`.`SuggestionOption`)) left join `users` `ad` on(`ad`.`UserId` = `s`.`ApproveDenyBy`))  ;
DROP TABLE IF EXISTS `vwusers`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwusers`  AS SELECT `u`.`UserId` AS `UserId`, `u`.`FirstName` AS `FirstName`, `u`.`LastName` AS `LastName`, `u`.`Email` AS `Email`, `u`.`PhoneNumber` AS `PhoneNumber`, `u`.`Password` AS `Password`, `u`.`IsAdmin` AS `IsAdmin`, `p`.`ProfileId` AS `ProfileId`, `p`.`MonthlySalary` AS `MonthlySalary`, `p`.`Budget` AS `Budget`, `p`.`Savings` AS `Savings` FROM (`users` `u` join `userprofile` `p` on(`p`.`UserId` = `u`.`UserId`))  ;


ALTER TABLE `bills`
  ADD PRIMARY KEY (`BillId`),
  ADD KEY `fk_companies_bill` (`CompanyId`);

ALTER TABLE `comments`
  ADD PRIMARY KEY (`CommentId`),
  ADD KEY `PostId` (`PostId`),
  ADD KEY `UserId` (`UserId`);

ALTER TABLE `companies`
  ADD PRIMARY KEY (`CompanyId`),
  ADD KEY `UserId` (`UserId`);

ALTER TABLE `error`
  ADD PRIMARY KEY (`ErrorId`);

ALTER TABLE `loans`
  ADD PRIMARY KEY (`LoanId`),
  ADD KEY `fk_loans_companies` (`CompanyId`);

ALTER TABLE `miscellaneous`
  ADD PRIMARY KEY (`MiscellaneousId`),
  ADD KEY `CompanyId` (`CompanyId`);

ALTER TABLE `paymenthistory`
  ADD PRIMARY KEY (`PaymentId`),
  ADD KEY `TypeId` (`TypeId`);

ALTER TABLE `posts`
  ADD PRIMARY KEY (`PostId`),
  ADD KEY `UserId` (`UserId`);

ALTER TABLE `replies`
  ADD PRIMARY KEY (`ReplyId`),
  ADD KEY `CommentId` (`CommentId`),
  ADD KEY `UserId` (`UserId`);

ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`SubscriptionId`),
  ADD KEY `CompanyId` (`CompanyId`);

ALTER TABLE `suggestions`
  ADD PRIMARY KEY (`SuggestionId`),
  ADD KEY `UserId` (`UserId`),
  ADD KEY `ApprovedBy` (`ApproveDenyBy`),
  ADD KEY `SuggestionOption` (`SuggestionOption`);

ALTER TABLE `types`
  ADD PRIMARY KEY (`TypeId`);

ALTER TABLE `usererror`
  ADD PRIMARY KEY (`UserId`,`ErrorId`),
  ADD KEY `Constr_UserError_Error` (`ErrorId`);

ALTER TABLE `userprofile`
  ADD PRIMARY KEY (`ProfileId`),
  ADD KEY `UserId` (`UserId`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`UserId`);

ALTER TABLE `watingoptions`
  ADD PRIMARY KEY (`OptionId`);


ALTER TABLE `bills`
  MODIFY `BillId` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `comments`
  MODIFY `CommentId` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `companies`
  MODIFY `CompanyId` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `error`
  MODIFY `ErrorId` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `loans`
  MODIFY `LoanId` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `miscellaneous`
  MODIFY `MiscellaneousId` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `paymenthistory`
  MODIFY `PaymentId` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `posts`
  MODIFY `PostId` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `replies`
  MODIFY `ReplyId` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `subscriptions`
  MODIFY `SubscriptionId` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `suggestions`
  MODIFY `SuggestionId` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `types`
  MODIFY `TypeId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `userprofile`
  MODIFY `ProfileId` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `UserId` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `watingoptions`
  MODIFY `OptionId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;


ALTER TABLE `bills`
  ADD CONSTRAINT `fk_companies_bill` FOREIGN KEY (`CompanyId`) REFERENCES `companies` (`CompanyId`);

ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`PostId`) REFERENCES `posts` (`PostId`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`UserId`) REFERENCES `users` (`UserId`);

ALTER TABLE `companies`
  ADD CONSTRAINT `companies_ibfk_2` FOREIGN KEY (`UserId`) REFERENCES `users` (`UserId`);

ALTER TABLE `loans`
  ADD CONSTRAINT `fk_loans_companies` FOREIGN KEY (`CompanyId`) REFERENCES `companies` (`CompanyId`);

ALTER TABLE `miscellaneous`
  ADD CONSTRAINT `miscellaneous_ibfk_1` FOREIGN KEY (`CompanyId`) REFERENCES `companies` (`CompanyId`);

ALTER TABLE `paymenthistory`
  ADD CONSTRAINT `paymenthistory_ibfk_1` FOREIGN KEY (`TypeId`) REFERENCES `types` (`TypeId`);

ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `users` (`UserId`);

ALTER TABLE `replies`
  ADD CONSTRAINT `replies_ibfk_1` FOREIGN KEY (`CommentId`) REFERENCES `comments` (`CommentId`),
  ADD CONSTRAINT `replies_ibfk_2` FOREIGN KEY (`UserId`) REFERENCES `users` (`UserId`);

ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`CompanyId`) REFERENCES `companies` (`CompanyId`);

ALTER TABLE `suggestions`
  ADD CONSTRAINT `suggestions_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `users` (`UserId`),
  ADD CONSTRAINT `suggestions_ibfk_2` FOREIGN KEY (`ApproveDenyBy`) REFERENCES `users` (`UserId`),
  ADD CONSTRAINT `suggestions_ibfk_3` FOREIGN KEY (`SuggestionOption`) REFERENCES `watingoptions` (`OptionId`);

ALTER TABLE `usererror`
  ADD CONSTRAINT `Constr_UserError_Error` FOREIGN KEY (`ErrorId`) REFERENCES `error` (`ErrorId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Constr_UserError_user` FOREIGN KEY (`UserId`) REFERENCES `users` (`UserId`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `userprofile`
  ADD CONSTRAINT `userprofile_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `users` (`UserId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
