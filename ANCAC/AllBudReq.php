<?php
	require("ulogin.php");
	require($root."dbconn.php");

	$page_title = 'ANCAC: All Budget Requests';
	require($root."header.php");

        $fiscalYear = $_SESSION['year'];
?>

<?php
                $sql = "SELECT centers.center, centers.CenterName FROM `centers` JOIN `eoyChecks` ON centers.center = eoyChecks.center".
                        " AND eoyChecks.fiscalyear = '".$fiscalYear."' AND eoyChecks.EstBudget = '1'".
                        "  WHERE centers.center not in (0,99) order by centers.center";
                $result = @mysql_query($sql) or mysql_error();
?>
                <center>
		<table class="OutlineTable" width="85%">
		        <tr><td class="login-header" colspan="6">All Submitted Budget Requests - FY <?php echo $fiscalYear; ?></td></tr>
                        <tr align="left"><td colspan="6"><b>Date: </b><?php echo date("M d Y"); ?></td></tr>
                        <tr><td colspan="6">&nbsp;</td></tr>
                        <tr><td colspan="6">
<?php
		while ($row = mysql_fetch_object($result)) {
                        $sqlBODCY = "SELECT sum(genFund) as genFund, sum(chilFirstTrust) as chilFirstTrust, sum(unitedWay) as unitedWay,".
                                " sum(adeca) as adeca, sum(natlChilAlliance) as natlChilAlliance, sum(chilTrustFund) as chilTrustFund,".
                                " sum(deptOfHR) as deptOfHR, sum(countyComm) as countyComm, sum(cityCouncil) as cityCouncil,".
                                " sum(localGrants) as localGrants, sum(areaSchools) as areaSchools, sum(corpDonations) as corpDonations,".
                                " sum(privDonations) as privDonations, sum(fundraisers) as fundraisers, sum(bankInterest) as bankInterest".
                                " FROM budgetedSourceFunds WHERE center = '".$row->center."' AND fiscalyear = '".$fiscalYear."'";

                        $resultBODCY = @mysql_query($sqlBODCY) or mysql_error();
                        $rowFunds = mysql_fetch_object($resultBODCY);
        
                        $sqlExp = "SELECT sum(personnelCosts) as personnelCosts, sum(empBenefits) as empBenefits, sum(travelInState) as travelInState,".
                                " sum(travelOutState) as travelOutState, sum(repairsAndMx) as repairsAndMx, sum(rentalsLease) as rentalsLease,".
                                " sum(utilComm) as utilComm, sum(profServ) as profServ, sum(suppMatOper) as suppMatOper,".
                                " sum(tranEqpPurch) as tranEqpPurch, sum(otherEqpPurch) as otherEqpPurch, sum(debtService) as debtService,".
                                " sum(misc) as misc, sum(capOutlay) as capOutlay".
                                " FROM budgetedExpenditures WHERE center = '".$row->center."' AND fiscalyear = '".$fiscalYear."'";
        
                        $resultExp = @mysql_query($sqlExp) or mysql_error();
                        $rowExp = mysql_fetch_object($resultExp);
        
                        $TotalRevenue = 0;
                        $TotalExpenditures = 0;
                        echo '<table width="100%" class="Admin">';
                        //Header
                                echo '<tr><td><b>Name of Child Advocacy Center: </b>'.$row->CenterName.'</td></tr>';
                                echo '<tr class="BoldText"><td>Revenue</td><td>&nbsp;</td></tr>';
                                //These will be all of the Standard Source Funds
                                 echo '<tr><td>State of AL General Fund</td><td align="right">$'.$rowFunds->genFund.'</td></tr>';
                                 $TotalRevenue = $TotalRevenue + $rowFunds->genFund;
                                 echo '<tr><td>State of AL Children First Trust</td><td align="right">$'.$rowFunds->chilFirstTrust.'</td></tr>';
                                 $TotalRevenue = $TotalRevenue + $rowFunds->chilFirstTrust;
                                 echo '<tr><td>United Way</td><td align="right">$'.$rowFunds->unitedWay.'</td></tr>';
                                 $TotalRevenue = $TotalRevenue + $rowFunds->unitedWay;
                                 echo '<tr><td>ADECA</td><td align="right">$'.$rowFunds->adeca.'</td></tr>';
                                 $TotalRevenue = $TotalRevenue + $rowFunds->adeca;
                                 echo '<tr><td>National Children\'s Alliance</td><td align="right">$'.$rowFunds->natlChilAlliance.'</td></tr>';
                                 $TotalRevenue = $TotalRevenue + $rowFunds->natlChilAlliance;
                                 echo '<tr><td>Children\'s Trust Fund</td><td align="right">$'.$rowFunds->chilTrustFund.'</td></tr>';
                                 $TotalRevenue = $TotalRevenue + $rowFunds->chilTrustFund;
                                 echo '<tr><td>Department of Human Resources</td><td align="right">$'.$rowFunds->deptOfHR.'</td></tr>';
                                 $TotalRevenue = $TotalRevenue + $rowFunds->deptOfHR;
                                 echo '<tr><td>County Commissions</td><td align="right">$'.$rowFunds->countyComm.'</td></tr>';
                                 $TotalRevenue = $TotalRevenue + $rowFunds->countyComm;
                                 echo '<tr><td>City Councils</td><td align="right">$'.$rowFunds->cityCouncil.'</td></tr>';
                                 $TotalRevenue = $TotalRevenue + $rowFunds->cityCouncil;
                                 echo '<tr><td>Local Grants</td><td align="right">$'.$rowFunds->localGrants.'</td></tr>';
                                 $TotalRevenue = $TotalRevenue + $rowFunds->localGrants;
                                 echo '<tr><td>Area Schools</td><td align="right">$'.$rowFunds->areaSchools.'</td></tr>';
                                 $TotalRevenue = $TotalRevenue + $rowFunds->areaSchools;
                                 echo '<tr><td>Corporate Donations</td><td align="right">$'.$rowFunds->corpDonations.'</td></tr>';
                                 $TotalRevenue = $TotalRevenue + $rowFunds->corpDonations;
                                 echo '<tr><td>Private Donations</td><td align="right">$'.$rowFunds->privDonations.'</td></tr>';
                                 $TotalRevenue = $TotalRevenue + $rowFunds->privDonations;
                                 echo '<tr><td>Fundraisers</td><td align="right">$'.$rowFunds->fundraisers.'</td></tr>';
                                 $TotalRevenue = $TotalRevenue + $rowFunds->fundraisers;
                                 echo '<tr><td>Bank Interest</td><td align="right">$'.$rowFunds->bankInterest.'</td></tr>';
                                 $TotalRevenue = $TotalRevenue + $rowFunds->bankInterest;
                                 //OTHER INCOMES
                                $sqlOI = "SELECT OIncomeID, IncomeName FROM otherIncomeLU WHERE center = '".$row->center."' AND fiscalyear = '".$fiscalYear."' ORDER BY OIncomeID";
                                $resultOI = @mysql_query($sqlOI) or mysql_error();
        
                                $numRecords = mysql_num_rows($resultOI);
                                if ($numRecords > 0){
                                        while ($rowOI = mysql_fetch_object($resultOI)) {
                                                $sqlOIValue = "SELECT sum(oiValue) as oiValue".
                                                " FROM budgetedOtherIncome WHERE center = '".$row->center."' AND fiscalyear = '".$fiscalYear."' AND OIncomeID = '".$rowOI->OIncomeID."'";
                                                $resultOIValue = @mysql_query($sqlOIValue) or mysql_error();
                                                $rowOIFunds = mysql_fetch_object($resultOIValue);
                                                echo '<tr><td>'.$rowOI->IncomeName.'</td><td align="right">$'.$rowOIFunds->oiValue.'</td></tr>';
                                                $TotalRevenue = $TotalRevenue + $rowOIFunds->oiValue;
                                        }
                                }
                                 echo '<tr class="BoldText"><td>Total Revenue</td><td align="right">$'.number_format ($TotalRevenue, 2).'</td></tr>';
                                 echo '<tr><td colspan="2">&nbsp;</td></tr>';
                                 echo '<tr class="BoldText"><td>Expenditures</td><td>&nbsp;</td></tr>';
                                //These will be all of the Standard Expenditures
                                 echo '<tr><td>Personnel Costs</td><td align="right">$'.$rowExp->personnelCosts.'</td></tr>';
                                 $TotalExpenditures = $TotalExpenditures + $rowExp->personnelCosts;
                                 echo '<tr><td>Employee Benefits</td><td align="right">$'.$rowExp->empBenefits.'</td></tr>';
                                 $TotalExpenditures = $TotalExpenditures + $rowExp->empBenefits;
                                 echo '<tr><td>Travel-In-State</td><td align="right">$'.$rowExp->travelInState.'</td></tr>';
                                 $TotalExpenditures = $TotalExpenditures + $rowExp->travelInState;
                                 echo '<tr><td>Travel-Out-of-State</td><td align="right">$'.$rowExp->travelOutState.'</td></tr>';
                                 $TotalExpenditures = $TotalExpenditures + $rowExp->travelOutState;
                                 echo '<tr><td>Repairs and Maintenance</td><td align="right">$'.$rowExp->repairsAndMx.'</td></tr>';
                                 $TotalExpenditures = $TotalExpenditures + $rowExp->repairsAndMx;
                                 echo '<tr><td>Rentals and Leases</td><td align="right">$'.$rowExp->rentalsLease.'</td></tr>';
                                 $TotalExpenditures = $TotalExpenditures + $rowExp->rentalsLease;
                                 echo '<tr><td>Utilities and Communications</td><td align="right">$'.$rowExp->utilComm.'</td></tr>';
                                 $TotalExpenditures = $TotalExpenditures + $rowExp->utilComm;
                                 echo '<tr><td>Professional Services</td><td align="right">$'.$rowExp->profServ.'</td></tr>';
                                 $TotalExpenditures = $TotalExpenditures + $rowExp->profServ;
                                 echo '<tr><td>Supplies, Materials, Operations</td><td align="right">$'.$rowExp->suppMatOper.'</td></tr>';
                                 $TotalExpenditures = $TotalExpenditures + $rowExp->suppMatOper;
                                 echo '<tr><td>Transportation Equip. Purchases</td><td align="right">$'.$rowExp->tranEqpPurch.'</td></tr>';
                                 $TotalExpenditures = $TotalExpenditures + $rowExp->tranEqpPurch;
                                 echo '<tr><td>Other Equipment Purchases</td><td align="right">$'.$rowExp->otherEqpPurch.'</td></tr>';
                                 $TotalExpenditures = $TotalExpenditures + $rowExp->otherEqpPurch;
                                 echo '<tr><td>Capital Outlay</td><td align="right">$'.$rowExp->capOutlay.'</td></tr>';
                                 $TotalExpenditures = $TotalExpenditures + $rowExp->capOutlay;
                                 echo '<tr><td>Debt Service</td><td align="right">$'.$rowExp->debtService.'</td></tr>';
                                 $TotalExpenditures = $TotalExpenditures + $rowExp->debtService;
                                 echo '<tr><td>Miscellaneous</td><td align="right">$'.$rowExp->misc.'</td></tr>';
                                 $TotalExpenditures = $TotalExpenditures + $rowExp->misc;
                                 //OTHER EXPENDITURES
                                 $sqlOE = "SELECT OExpenseID, ExpenseName FROM otherExpenseLU WHERE center = '".$row->center."' AND fiscalyear = '".$fiscalYear."' ORDER BY OExpenseID";
                                $resultOE = @mysql_query($sqlOE) or mysql_error();
        
                                $numRecords = mysql_num_rows($resultOE);
                                if ($numRecords > 0){
                                        while ($rowOE = mysql_fetch_object($resultOE)) {
                                                $sqlOEValue = "SELECT sum(oeValue) as oeValue".
                                                " FROM budgetedOtherExpense WHERE center = '".$row->center."' AND fiscalyear = '".$fiscalYear."' AND OExpenseID = '".$rowOE->OExpenseID."'";
                                                $resultOEValue = @mysql_query($sqlOEValue) or mysql_error();
                                                $rowOEFunds = mysql_fetch_object($resultOEValue);
                                                echo '<tr><td>'.$rowOE->ExpenseName.'</td><td align="right">$'.$rowOEFunds->oeValue.'</td></tr>';
                                                $TotalExpenditures = $TotalExpenditures + $rowOEFunds->oeValue;
                                        }
                                }
        
                                 echo '<tr class="BoldText"><td>Total Expenditures</td><td align="right">$'.number_format ($TotalExpenditures, 2).'</td></tr>';
                        echo '</table>';
                        echo '<br />';
                        echo '<div class="page-break"></div>';
                }
?>
                </td></tr>
                </table>
                </center>



</html>

