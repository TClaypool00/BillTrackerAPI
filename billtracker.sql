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
CREATE DEFINER=`root`@`localhost` PROCEDURE `delMisc` (IN `id` INT(11))  DELETE FROM miscellaneous where MiscellaneousId = id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insBill` (IN `bill_name` VARCHAR(255), IN `amount_due` DECIMAL(15,2), IN `company_id` INT(11), IN `date_due` DATE)  BEGIN
	DECLARE billId INT;

    INSERT INTO bills (BillName, AmountDue, CompanyId)
    VALUES (bill_name, amount_due, company_id);

    SET billId = LAST_INSERT_ID();
    
    INSERT INTO paymenthistory (ExpenseId, TypeId, DateDue)
    VALUES (billId, 1, date_due);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insCompany` (IN `company_name` VARCHAR(255), IN `user_id` INT(11))  INSERT INTO companies (CompanyName, UserId)
VALUES (company_name, user_id)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insLoan` (IN `loan_name` VARCHAR(255), IN `monthly_amt_due` DECIMAL(11,2), IN `total_loan_amt` DECIMAL(11,2), IN `remaining_amt` DECIMAL(11,2), IN `company_id` INT(11), IN `date_due` DATE)  BEGIN
	DECLARE loanId INT;

	INSERT INTO loans (LoanName, MonthlyAmountDue, TotalAmountDue, RemainingAmount, CompanyId)
	VALUES (loan_name, monthly_amt_due, total_loan_amt, remaining_amt, company_id);
    
    SET loanId = LAST_INSERT_ID();
    
    INSERT INTO paymenthistory (ExpenseId, TypeId, DateDue)
    VALUES (loanId, 2, date_due);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insMisc` (IN `name` VARCHAR(255), IN `amount` DECIMAL(11,2), IN `company_id` INT(11))  BEGIN
	DECLARE miscId INT;

	INSERT INTO miscellaneous (Name, Amount, CompanyId)
	VALUES (name, amount, company_id);
    
    SET miscId = LAST_INSERT_ID();
    
    INSERT INTO paymenthistory (ExpenseId, TypeId)
    VALUES (miscId, 4);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insPaymentHistory` (IN `expense_id` INT(11), IN `type_id` INT(11))  INSERT INTO paymenthistory (ExpenseId, TypeId)
VALUES (expense_id, type_id)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insSub` (IN `name` VARCHAR(255), IN `amount_due` DECIMAL(11,2), IN `due_date` DATE, IN `company_id` INT)  BEGIN
	DECLARE subId INT;

	INSERT INTO subscriptions (Name, AmountDue, CompanyId)
    VALUES (name, amount_due, company_id);
    
    SET subId = LAST_INSERT_ID();
    
    INSERT INTO paymenthistory (ExpenseId, TypeId, DateDue)
    VALUES (subId, 3, due_date);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insUser` (IN `first_name` VARCHAR(255), IN `last_name` VARCHAR(255), IN `email` VARCHAR(255), IN `password` VARCHAR(255), IN `phone_num` VARCHAR(10))  BEGIN
	DECLARE user_id INT;
	INSERT INTO users (FirstName, LastName, Email, Password, IsAdmin, PhoneNumber)
    VALUES (first_name, last_name, email, password, FALSE, phone_num);
    
    SET user_id = LAST_INSERT_ID();
    
    INSERT INTO userprofile (UserId)
    VALUES (user_id);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `selCompanyDropDown` (IN `user_id` INT(11))  SELECT c.CompanyId, c.CompanyName FROM companies c
WHERE c.UserId = user_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateProfile` (IN `monthly_salary` DOUBLE(11,2), IN `budget` DOUBLE(11,2), IN `saving` DOUBLE(11,2), IN `user_id` INT(11))  UPDATE userprofile
SET MonthlySalary = monthly_salary, Budget = budget, Savings = saving
WHERE UserId = user_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updBill` (IN `bill_name` VARCHAR(255), IN `amount_due` DECIMAL(11,2), IN `is_recurring` BOOLEAN, IN `is_active` BOOLEAN, IN `end_date` DATE, IN `bill_id` INT(11))  UPDATE bills
SET BillName = bill_name, AmountDue = amount_due, IsRecurring = is_recurring, IsActive = is_active, EndDate = end_date
WHERE BillId = bill_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updCompanyName` (IN `company_name` VARCHAR(255), IN `company_id` INT(11))  UPDATE companies
SET CompanyName = company_name
WHERE CompanyId = company_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updLoan` (IN `loan_name` VARCHAR(255), IN `is_active` BOOLEAN, IN `monthly_amt_due` DECIMAL(11,2), IN `total_amt_due` DECIMAL(11,2), IN `remaining_amt_due` DECIMAL(11,2), IN `loan_id` INT(11))  UPDATE loans
SET IsActive = is_active, MonthlyAmountDue = monthly_amt_due, TotalAmountDue = total_amt_due, RemainingAmount = remaining_amt_due
WHERE LoanId = loan_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updMisc` (IN `name` VARCHAR(255), IN `amount` DECIMAL(11,2), IN `company_id` INT(11), IN `id` INT(11))  UPDATE miscellaneous
SET Name = name, Amount = amount, CompanyId = company_id
WHERE MiscellaneousId = id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updPayExpense` (IN `expense_id` INT(11), IN `type_id` INT(11))  BEGIN
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `updSub` (IN `name` VARCHAR(255), IN `amount_due` DECIMAL(11,2), IN `due_date` DATE, IN `is_active` BOOLEAN, IN `company_id` INT, IN `id` INT)  BEGIN
    UPDATE subscriptions
    SET Name = name, AmountDue = amount_due, IsActive = is_active, CompanyId = company_id
    WHERE SubscriptionId = id;

    UPDATE paymenthistory
    SET DateDue = due_date
    WHERE ExpenseId = id AND TypeId = 3 AND (MONTH(DateDue) = MONTH(NOW()) AND YEAR(DateDue) = YEAR(NOW()));
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updUser` (IN `first_name` VARCHAR(255), IN `last_name` VARCHAR(255), IN `email` VARCHAR(255), IN `phone_num` VARCHAR(10), IN `user_id` INT(11))  UPDATE users
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

CREATE TABLE `companies` (
  `CompanyId` int(11) NOT NULL,
  `CompanyName` varchar(255) NOT NULL,
  `UserId` int(11) NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1
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

CREATE TABLE `subscriptions` (
  `SubscriptionId` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `AmountDue` decimal(11,2) NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `CompanyId` int(11) NOT NULL
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
DROP TABLE IF EXISTS `vwbills`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwbills`  AS SELECT `b`.`BillId` AS `BillId`, `b`.`BillName` AS `BillName`, `b`.`AmountDue` AS `AmountDue`, `b`.`IsActive` AS `IsActive`, `h`.`DateDue` AS `DateDue`, `h`.`DatePaid` AS `DatePaid`, `h`.`IsPaid` AS `IsPaid`, `h`.`IsLate` AS `IsLate`, `b`.`CompanyId` AS `CompanyId`, `c`.`CompanyName` AS `CompanyName`, `c`.`UserId` AS `UserId`, `u`.`FirstName` AS `FirstName`, `u`.`LastName` AS `LastName` FROM (((`bills` `b` join `companies` `c` on(`c`.`CompanyId` = `b`.`CompanyId`)) join `users` `u` on(`u`.`UserId` = `c`.`UserId`)) join `paymenthistory` `h` on(`h`.`ExpenseId` = `b`.`BillId` and `h`.`TypeId` = 1 and month(`h`.`DateDue`) = month(current_timestamp()) and year(`h`.`DateDue`) = year(current_timestamp()))) ;
DROP TABLE IF EXISTS `vwcompanies`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwcompanies`  AS SELECT `c`.`CompanyId` AS `CompanyId`, `c`.`CompanyName` AS `CompanyName`, `c`.`UserId` AS `UserId`, `c`.`IsActive` AS `IsActive`, `u`.`FirstName` AS `FirstName`, `u`.`LastName` AS `LastName` FROM (`companies` `c` join `users` `u` on(`u`.`UserId` = `c`.`UserId`)) ;
DROP TABLE IF EXISTS `vwloans`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwloans`  AS SELECT `l`.`LoanId` AS `LoanId`, `l`.`LoanName` AS `LoanName`, `l`.`IsActive` AS `IsActive`, `l`.`MonthlyAmountDue` AS `MonthlyAmountDue`, `l`.`TotalAmountDue` AS `TotalAmountDue`, `l`.`RemainingAmount` AS `RemainingAmount`, `h`.`DateDue` AS `DateDue`, `h`.`DatePaid` AS `DatePaid`, `h`.`IsPaid` AS `IsPaid`, `h`.`IsLate` AS `IsLate`, `l`.`CompanyId` AS `CompanyId`, `c`.`CompanyName` AS `CompanyName`, `c`.`UserId` AS `UserId`, `u`.`FirstName` AS `FirstName`, `u`.`LastName` AS `LastName` FROM (((`loans` `l` join `companies` `c` on(`c`.`CompanyId` = `l`.`CompanyId`)) join `users` `u` on(`u`.`UserId` = `c`.`UserId`)) join `paymenthistory` `h` on(`h`.`ExpenseId` = `l`.`LoanId` and `h`.`TypeId` = 2 and month(`h`.`DateDue`) = month(current_timestamp()) and year(`h`.`DateDue`) = year(current_timestamp()))) ;
DROP TABLE IF EXISTS `vwmiscellaneous`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwmiscellaneous`  AS SELECT `m`.`MiscellaneousId` AS `MiscellaneousId`, `m`.`Name` AS `Name`, `m`.`Amount` AS `Amount`, `m`.`DateAdded` AS `DateAdded`, `m`.`CompanyId` AS `CompanyId`, `c`.`CompanyName` AS `CompanyName`, `u`.`UserId` AS `UserId`, `u`.`FirstName` AS `FirstName`, `u`.`LastName` AS `LastName` FROM ((`miscellaneous` `m` join `companies` `c` on(`c`.`CompanyId` = `m`.`CompanyId`)) join `users` `u` on(`u`.`UserId` = `c`.`UserId`)) ;
DROP TABLE IF EXISTS `vwusers`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwusers`  AS SELECT `u`.`UserId` AS `UserId`, `u`.`FirstName` AS `FirstName`, `u`.`LastName` AS `LastName`, `u`.`Email` AS `Email`, `u`.`PhoneNumber` AS `PhoneNumber`, `u`.`Password` AS `Password`, `u`.`IsAdmin` AS `IsAdmin`, `p`.`ProfileId` AS `ProfileId`, `p`.`MonthlySalary` AS `MonthlySalary`, `p`.`Budget` AS `Budget`, `p`.`Savings` AS `Savings` FROM (`users` `u` join `userprofile` `p` on(`p`.`UserId` = `u`.`UserId`)) ;
DROP TABLE IF EXISTS `vwsubscriptions`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vwsubscriptions`  AS SELECT `s`.`SubscriptionId` AS `SubscriptionId`, `s`.`Name` AS `Name`, `s`.`AmountDue` AS `AmountDue`, `s`.`IsActive` AS `IsActive`, `h`.`DateDue` AS `DateDue`, `h`.`DatePaid` AS `DatePaid`, `h`.`IsPaid` AS `IsPaid`, `h`.`IsLate` AS `IsLate`, `s`.`CompanyId` AS `CompanyId`, `c`.`CompanyName` AS `CompanyName`, `c`.`UserId` AS `UserId`, `u`.`FirstName` AS `FirstName`, `u`.`LastName` AS `LastName` FROM (((`subscriptions` `s` join `companies` `c` on(`c`.`CompanyId` = `s`.`CompanyId`)) join `users` `u` on(`u`.`UserId` = `c`.`UserId`)) join `paymenthistory` `h` on(`h`.`ExpenseId` = `s`.`SubscriptionId` and `h`.`TypeId` = 3 and month(`h`.`DateDue`) = month(current_timestamp()) and year(`h`.`DateDue`) = year(current_timestamp()))) ;


ALTER TABLE `bills`
  ADD PRIMARY KEY (`BillId`),
  ADD KEY `fk_companies_bill` (`CompanyId`);

ALTER TABLE `companies`
  ADD PRIMARY KEY (`CompanyId`),
  ADD KEY `UserId` (`UserId`);

ALTER TABLE `loans`
  ADD PRIMARY KEY (`LoanId`),
  ADD KEY `fk_loans_companies` (`CompanyId`);

ALTER TABLE `miscellaneous`
  ADD PRIMARY KEY (`MiscellaneousId`),
  ADD KEY `CompanyId` (`CompanyId`);

ALTER TABLE `paymenthistory`
  ADD PRIMARY KEY (`PaymentId`),
  ADD KEY `TypeId` (`TypeId`);

ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`SubscriptionId`),
  ADD KEY `CompanyId` (`CompanyId`);

ALTER TABLE `types`
  ADD PRIMARY KEY (`TypeId`);

ALTER TABLE `userprofile`
  ADD PRIMARY KEY (`ProfileId`),
  ADD KEY `UserId` (`UserId`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`UserId`);


ALTER TABLE `bills`
  MODIFY `BillId` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `companies`
  MODIFY `CompanyId` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `loans`
  MODIFY `LoanId` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `miscellaneous`
  MODIFY `MiscellaneousId` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `paymenthistory`
  MODIFY `PaymentId` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `subscriptions`
  MODIFY `SubscriptionId` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `types`
  MODIFY `TypeId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `userprofile`
  MODIFY `ProfileId` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `UserId` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `bills`
  ADD CONSTRAINT `fk_companies_bill` FOREIGN KEY (`CompanyId`) REFERENCES `companies` (`CompanyId`);

ALTER TABLE `companies`
  ADD CONSTRAINT `companies_ibfk_2` FOREIGN KEY (`UserId`) REFERENCES `users` (`UserId`);

ALTER TABLE `loans`
  ADD CONSTRAINT `fk_loans_companies` FOREIGN KEY (`CompanyId`) REFERENCES `companies` (`CompanyId`);

ALTER TABLE `miscellaneous`
  ADD CONSTRAINT `miscellaneous_ibfk_1` FOREIGN KEY (`CompanyId`) REFERENCES `companies` (`CompanyId`);

ALTER TABLE `paymenthistory`
  ADD CONSTRAINT `paymenthistory_ibfk_1` FOREIGN KEY (`TypeId`) REFERENCES `types` (`TypeId`);

ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`CompanyId`) REFERENCES `companies` (`CompanyId`);

ALTER TABLE `userprofile`
  ADD CONSTRAINT `userprofile_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `users` (`UserId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
